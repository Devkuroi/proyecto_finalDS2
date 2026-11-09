<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class Entrega1ViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_vista_login_se_renderiza_correctamente(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('MODO DEMOSTRACIÓN');
        $response->assertSee('ventanilla17@cali.gov.co');
    }

    public function test_vista_ventanilla_se_renderiza_para_operador_ventanilla(): void
    {
        $userVentanilla = User::where('email', 'ventanilla17@cali.gov.co')->first();
        $response = $this->actingAs($userVentanilla)->get('/ventanilla');
        $response->assertStatus(200);
        $response->assertSee('Módulo de Radicación en Ventanilla');
        $response->assertSee('Georreferenciador Territorial de Cali');
    }

    public function test_vista_bitacora_se_renderiza_para_enrutador(): void
    {
        $userEnrutador = User::where('email', 'enrutador@cali.gov.co')->first();
        $response = $this->actingAs($userEnrutador)->get('/enrutador/bitacora');
        $response->assertStatus(200);
        $response->assertSee('Bitácora Diaria del Enrutador');
        $response->assertSee('Mapa de Consolidación Territorial Diaria');
    }

    public function test_vistas_placeholder_se_renderizan_para_encuestador_calidad_admin(): void
    {
        $rolesPrueba = [
            'encuestador1@cali.gov.co' => 'ENCUESTADOR',
            'calidad@cali.gov.co' => 'CALIDAD',
            'admin@cali.gov.co' => 'ADMINISTRADOR',
        ];

        foreach ($rolesPrueba as $email => $rol) {
            $user = User::where('email', $email)->first();
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
            $response->assertSee($rol);
            $response->assertSee('Sprint 2');
        }
    }
}
