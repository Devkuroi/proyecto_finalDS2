<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Rol;
use App\Models\PuntoAtencion;
use App\Models\ComunaCorregimiento;
use App\Models\BarrioVereda;
use App\Models\SolicitudEncuesta;
use Carbon\Carbon;

class Entrega1BackendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Precargar datos oficiales de Entrega 1 en la base de datos de pruebas
        $this->seed();
    }

    /**
     * Requerimiento 1: Autenticación exitosa de los 5 perfiles institucionales
     */
    public function test_login_con_credenciales_validas_y_los_5_roles(): void
    {
        $roles = [
            'admin@cali.gov.co' => 'ADMINISTRADOR',
            'ventanilla17@cali.gov.co' => 'VENTANILLA',
            'encuestador1@cali.gov.co' => 'ENCUESTADOR',
            'calidad@cali.gov.co' => 'CALIDAD',
            'enrutador@cali.gov.co' => 'ENRUTADOR',
        ];

        foreach ($roles as $email => $rolEsperado) {
            $response = $this->postJson('/api/auth/login', [
                'email' => $email,
                'password' => 'Password123!',
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'user' => [
                        'email' => $email,
                        'rol' => $rolEsperado,
                    ],
                ]);

            $this->assertAuthenticated();
            $this->post('/logout');
        }
    }

    /**
     * Requerimiento 1: Rechazo de credenciales incorrectas
     */
    public function test_login_rechaza_credenciales_invalidas(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'ventanilla17@cali.gov.co',
            'password' => 'PasswordErronea999',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertGuest();
    }

    /**
     * Requerimiento 2: Catálogo territorial de Cali retorna Comunas (1-22) y Corregimientos
     */
    public function test_catalogo_territorial_retorna_comunas_y_corregimientos(): void
    {
        $response = $this->getJson('/api/territorio/comunas-corregimientos');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(37, count($data)); // 22 comunas + 15 corregimientos
    }

    /**
     * Requerimiento 2: Filtrado dinámico de barrios estrictamente asociados a una comuna
     */
    public function test_catalogo_barrios_filtra_estrictamente_por_comuna(): void
    {
        // Consultar barrios de Comuna 17 (El Ingenio, Pasoancho, etc.)
        $response = $this->getJson('/api/territorio/barrios?comuna_id=17');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'comuna_corregimiento_id' => 17,
            ]);

        $barrios = $response->json('data');
        $this->assertNotEmpty($barrios);

        foreach ($barrios as $barrio) {
            $this->assertEquals(17, $barrio['comuna_corregimiento_id']);
        }
    }

    /**
     * Requerimiento 5 (Precarga de Sedes): Listado de puntos de atención oficiales
     */
    public function test_puntos_atencion_retorna_sedes_oficiales_cali(): void
    {
        $response = $this->getJson('/api/puntos-atencion');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $sedes = $response->json('data');
        $comunasSedes = array_column($sedes, 'comuna_numero');

        // Sedes exigidas: Comunas 1, 6, 7, 13, 14, 20, 21 y 17 (Sede Principal)
        $sedesEsperadas = [1, 6, 7, 13, 14, 17, 20, 21];
        foreach ($sedesEsperadas as $comunaNum) {
            $this->assertContains($comunaNum, $comunasSedes);
        }
    }

    /**
     * Requerimientos 2 y 3: Radicación exitosa de solicitud con georreferenciación en Ventanilla
     */
    public function test_ventanilla_puede_radicar_solicitud_georreferenciada(): void
    {
        $userVentanilla = User::where('email', 'ventanilla17@cali.gov.co')->first();

        $payload = [
            'nombres_solicitante' => 'Carlos Andrés',
            'apellidos_solicitante' => 'Pérez Murillo',
            'celular' => '3158901234',
            'direccion' => 'Calle 13B # 85-20, Casa 4',
            'comuna_corregimiento_id' => 17,
            'barrio_id' => 60, // Pasoancho (Comuna 17)
            'latitud' => 3.3768,
            'longitud' => -76.5369,
            'punto_atencion_id' => 1,
            'observaciones' => 'Prueba de radicación automatizada',
        ];

        $response = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'solicitante' => 'Carlos Andrés Pérez Murillo',
                    'estado' => 'RADICADA',
                    'dias_restantes' => 15, // Cálculo de política +15 días
                ],
            ]);

        $this->assertDatabaseHas('solicitudes_encuesta', [
            'nombres_solicitante' => 'Carlos Andrés',
            'celular' => '3158901234',
            'estado' => 'RADICADA',
        ]);
    }

    /**
     * Requerimientos 2 y 3: Incoherencia territorial (barrio no pertenece a comuna) es rechazada
     */
    public function test_ventanilla_rechaza_incoherencia_entre_barrio_y_comuna(): void
    {
        $userVentanilla = User::where('email', 'ventanilla17@cali.gov.co')->first();

        // Barrio 1 (Terrón Colorado en Comuna 1) pero indicando Comuna 17
        $payload = [
            'nombres_solicitante' => 'Incoherencia',
            'apellidos_solicitante' => 'Territorial',
            'celular' => '3158901234',
            'direccion' => 'Calle Falsa 123',
            'comuna_corregimiento_id' => 17, // Comuna 17
            'barrio_id' => 1, // Barrio de Comuna 1
            'latitud' => 3.3768,
            'longitud' => -76.5369,
            'punto_atencion_id' => 1,
        ];

        $response = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Validación de Georreferenciación: Coordenadas fuera de Cali o en municipios vecinos son rechazadas
     */
    public function test_ventanilla_rechaza_georreferenciacion_fuera_de_cali(): void
    {
        $userVentanilla = User::where('email', 'ventanilla17@cali.gov.co')->first();

        // 1. Coordenadas de Bogotá (Lat 4.7110, Lng -74.0721)
        $payloadBogota = [
            'nombres_solicitante' => 'Pedro',
            'apellidos_solicitante' => 'Gómez',
            'celular' => '3151234567',
            'direccion' => 'Calle 13B # 85-20, Casa 4',
            'comuna_corregimiento_id' => 17,
            'barrio_id' => 60, // Pasoancho
            'latitud' => 4.7110,
            'longitud' => -74.0721,
            'punto_atencion_id' => 1,
        ];

        $responseBogota = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payloadBogota);

        $responseBogota->assertStatus(422)
            ->assertJsonValidationErrors(['latitud', 'longitud']);

        // 2. Coordenadas de Jamundí (Lat 3.2605 < 3.3000, rechazada por latitud fuera de Cali)
        $payloadJamundi = array_merge($payloadBogota, [
            'latitud' => 3.2605,
            'longitud' => -76.5414,
        ]);
        $responseJamundi = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payloadJamundi);
        $responseJamundi->assertStatus(422)
            ->assertJsonValidationErrors(['latitud']);

        // 3. Coordenadas de Yumbo Centro (Lat 3.5820 > 3.5300, rechazada por latitud fuera de Cali)
        $payloadYumbo = array_merge($payloadBogota, [
            'latitud' => 3.5820,
            'longitud' => -76.4950,
        ]);
        $responseYumbo = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payloadYumbo);
        $responseYumbo->assertStatus(422)
            ->assertJsonValidationErrors(['latitud']);

        // 4. Coordenadas de Palmira Centro (Lat 3.5390, Lng -76.3030, rechazada por latitud y longitud)
        $payloadPalmira = array_merge($payloadBogota, [
            'latitud' => 3.5390,
            'longitud' => -76.3030,
        ]);
        $responsePalmira = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payloadPalmira);
        $responsePalmira->assertStatus(422)
            ->assertJsonValidationErrors(['latitud', 'longitud']);

        // 5. Coordenadas de Acopi/Yumbo (Lat 3.518, Lng -76.518, filtro de exclusión de municipio vecino)
        $payloadAcopi = array_merge($payloadBogota, [
            'latitud' => 3.5180,
            'longitud' => -76.5180,
        ]);
        $responseAcopi = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', $payloadAcopi);
        $responseAcopi->assertStatus(422)
            ->assertJsonValidationErrors(['latitud', 'longitud']);
    }

    /**
     * Validación de Dirección: Direcciones fuera de Cali o con formato inválido son rechazadas
     */
    public function test_ventanilla_rechaza_direccion_fuera_de_cali_o_invalida(): void
    {
        $userVentanilla = User::where('email', 'ventanilla17@cali.gov.co')->first();

        // 1. Dirección explícita de Bogotá
        $responseBogota = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', [
                'nombres_solicitante' => 'Ana',
                'apellidos_solicitante' => 'Torres',
                'celular' => '3151234567',
                'direccion' => 'Carrera 7 # 72-01, Bogotá',
                'comuna_corregimiento_id' => 17,
                'barrio_id' => 60,
                'latitud' => 3.3768,
                'longitud' => -76.5369,
                'punto_atencion_id' => 1,
            ]);

        $responseBogota->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        // 2. Dirección sin nomenclatura válida
        $responseInvalida = $this->actingAs($userVentanilla)
            ->postJson('/api/solicitudes', [
                'nombres_solicitante' => 'Ana',
                'apellidos_solicitante' => 'Torres',
                'celular' => '3151234567',
                'direccion' => 'texto_aleatorio_sin_formato',
                'comuna_corregimiento_id' => 17,
                'barrio_id' => 60,
                'latitud' => 3.3768,
                'longitud' => -76.5369,
                'punto_atencion_id' => 1,
            ]);

        $responseInvalida->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Requerimiento 4: Bitácora Diaria del Enrutador consolida solicitudes del día
     */
    public function test_enrutador_puede_consultar_bitacora_diaria_consolidada(): void
    {
        $userEnrutador = User::where('email', 'enrutador@cali.gov.co')->first();

        $response = $this->actingAs($userEnrutador)
            ->getJson('/api/enrutador/bitacora-diaria');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);

        // Verificar que los campos exigidos están presentes en la bitácora
        $primeraSolicitud = $data[0];
        $this->assertArrayHasKey('radicado_codigo', $primeraSolicitud);
        $this->assertArrayHasKey('solicitante', $primeraSolicitud);
        $this->assertArrayHasKey('comuna_nombre', $primeraSolicitud);
        $this->assertArrayHasKey('barrio_nombre', $primeraSolicitud);
        $this->assertArrayHasKey('latitud', $primeraSolicitud);
        $this->assertArrayHasKey('longitud', $primeraSolicitud);
        $this->assertArrayHasKey('hora_radicacion', $primeraSolicitud);
        $this->assertArrayHasKey('dias_restantes', $primeraSolicitud);
    }

    /**
     * Control de Acceso por Roles: Perfiles sin permisos son bloqueados
     */
    public function test_usuarios_no_autorizados_son_bloqueados_en_ventanilla_y_enrutador(): void
    {
        $userEncuestador = User::where('email', 'encuestador1@cali.gov.co')->first();

        // Encuestador intentando acceder a ventanilla
        $responseVentanilla = $this->actingAs($userEncuestador)->get('/ventanilla');
        $responseVentanilla->assertStatus(302); // Redirige al dashboard

        // Encuestador intentando acceder a la API de bitácora del enrutador
        $responseBitacora = $this->actingAs($userEncuestador)->getJson('/api/enrutador/bitacora-diaria');
        $responseBitacora->assertStatus(403);
    }
}
