<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with Entrega 1 requirements.
     */
    public function run(): void
    {
        // Deshabilitar temporalmente checks de llaves foráneas para reinicio limpio
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        }

        DB::table('solicitudes_encuesta')->delete();
        DB::table('barrios_veredas')->delete();
        DB::table('comunas_corregimientos')->delete();
        DB::table('usuarios')->delete();
        DB::table('puntos_atencion')->delete();
        DB::table('roles')->delete();

        // 1. Roles Institucionales (5 perfiles exigidos)
        $roles = [
            ['id' => 1, 'nombre' => 'ADMINISTRADOR', 'descripcion' => 'Administrador general del sistema institucional y control maestro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'VENTANILLA', 'descripcion' => 'Operador de atención al ciudadano y radicación georreferenciada en sede', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'ENCUESTADOR', 'descripcion' => 'Personal operativo de campo para levantamiento de encuestas domiciliarias', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'CALIDAD', 'descripcion' => 'Auditor de calidad y verificación metodológica de encuestas', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nombre' => 'ENRUTADOR', 'descripcion' => 'Coordinador logístico de bitácora diaria y consolidación territorial', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('roles')->insert($roles);

        // 2. Puntos de Atención Oficiales en Cali (Comunas 1, 6, 7, 13, 14, 20, 21 y Comuna 17 - Sede Principal)
        $puntosAtencion = [
            [
                'id' => 1,
                'nombre' => 'Sede Principal - Oficina Central (CALI 17)',
                'codigo' => 'SEDE-COMUNA-17-PRINCIPAL',
                'comuna_numero' => 17,
                'direccion' => 'Calle 13B # 85-10, Barrio Pasoancho',
                'telefono' => '(602) 8879020',
                'latitud' => 3.37652400,
                'longitud' => -76.53671200,
                'es_principal' => 1,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Punto de Atención CALI 1 - Terrón Colorado',
                'codigo' => 'SEDE-COMUNA-01',
                'comuna_numero' => 1,
                'direccion' => 'Avenida 4A Oeste # 19-00, Terrón Colorado',
                'telefono' => '(602) 8933333',
                'latitud' => 3.45321800,
                'longitud' => -76.55621400,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nombre' => 'Punto de Atención CALI 6 - Los Guaduales',
                'codigo' => 'SEDE-COMUNA-06',
                'comuna_numero' => 6,
                'direccion' => 'Carrera 8N # 70A-16, Los Guaduales',
                'telefono' => '(602) 4481234',
                'latitud' => 3.48624100,
                'longitud' => -76.49503200,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nombre' => 'Punto de Atención CALI 7 - Alfonso López',
                'codigo' => 'SEDE-COMUNA-07',
                'comuna_numero' => 7,
                'direccion' => 'Calle 73 # 7G-34, Alfonso López I',
                'telefono' => '(602) 4415566',
                'latitud' => 3.46581200,
                'longitud' => -76.48804500,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'nombre' => 'Punto de Atención CALI 13 - El Poblado',
                'codigo' => 'SEDE-COMUNA-13',
                'comuna_numero' => 13,
                'direccion' => 'Calle 72P con Carrera 28D, El Poblado II',
                'telefono' => '(602) 4208899',
                'latitud' => 3.41872300,
                'longitud' => -76.49651800,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'nombre' => 'Punto de Atención CALI 14 - Marroquín',
                'codigo' => 'SEDE-COMUNA-14',
                'comuna_numero' => 14,
                'direccion' => 'Carrera 27 # 78A-04, Marroquín II',
                'telefono' => '(602) 4492211',
                'latitud' => 3.42514500,
                'longitud' => -76.48202900,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'nombre' => 'Punto de Atención CALI 20 - Siloé',
                'codigo' => 'SEDE-COMUNA-20',
                'comuna_numero' => 20,
                'direccion' => 'Carrera 52 con Calle 2, Siloé',
                'telefono' => '(602) 5521098',
                'latitud' => 3.42051200,
                'longitud' => -76.55403100,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'nombre' => 'Punto de Atención CALI 21 - Desepaz',
                'codigo' => 'SEDE-COMUNA-21',
                'comuna_numero' => 21,
                'direccion' => 'Carrera 23 con Calle 120, Desepaz',
                'telefono' => '(602) 4307766',
                'latitud' => 3.43201400,
                'longitud' => -76.47102500,
                'es_principal' => 0,
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('puntos_atencion')->insert($puntosAtencion);

        // 3. Usuarios de prueba (1 por cada rol con password Password123!)
        $hashPassword = Hash::make('Password123!');
        $usuarios = [
            [
                'id' => 1,
                'nombre' => 'Ing. Carlos Mendoza',
                'email' => 'admin@cali.gov.co',
                'password' => $hashPassword,
                'rol_id' => 1, // ADMINISTRADOR
                'punto_atencion_id' => 1,
                'identificacion' => '1144123001',
                'telefono' => '3001234501',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'María Fernanda Gómez',
                'email' => 'ventanilla17@cali.gov.co',
                'password' => $hashPassword,
                'rol_id' => 2, // VENTANILLA (Comuna 17)
                'punto_atencion_id' => 1,
                'identificacion' => '1144123002',
                'telefono' => '3001234502',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nombre' => 'Julián Andrés Caicedo',
                'email' => 'ventanilla1@cali.gov.co',
                'password' => $hashPassword,
                'rol_id' => 2, // VENTANILLA (Comuna 1)
                'punto_atencion_id' => 2,
                'identificacion' => '1144123003',
                'telefono' => '3001234503',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nombre' => 'Pedro Antonio Ramos',
                'email' => 'encuestador1@cali.gov.co',
                'password' => $hashPassword,
                'rol_id' => 3, // ENCUESTADOR
                'punto_atencion_id' => 1,
                'identificacion' => '1144123004',
                'telefono' => '3001234504',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'nombre' => 'Dra. Patricia Salazar',
                'email' => 'calidad@cali.gov.co',
                'password' => $hashPassword,
                'rol_id' => 4, // CALIDAD
                'punto_atencion_id' => 1,
                'identificacion' => '1144123005',
                'telefono' => '3001234505',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'nombre' => 'Ing. Roberto Quintero',
                'email' => 'enrutador@cali.gov.co',
                'password' => $hashPassword,
                'rol_id' => 5, // ENRUTADOR
                'punto_atencion_id' => 1,
                'identificacion' => '1144123006',
                'telefono' => '3001234506',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('usuarios')->insert($usuarios);

        // 4. Catálogo Territorial: 22 Comunas Urbanas y 15 Corregimientos Rurales
        $comunas = [];
        for ($i = 1; $i <= 22; $i++) {
            $nombreComuna = "Comuna {$i}";
            $comunas[] = [
                'id' => $i,
                'tipo' => 'COMUNA',
                'numero' => $i,
                'nombre' => "{$nombreComuna} - Santiago de Cali",
                'zona' => 'URBANA',
                'created_at' => now(),
            ];
        }

        $corregimientos = [
            ['id' => 23, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Pance', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 24, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento La Buitrera', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 25, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Montebello', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 26, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Golondrinas', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 27, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Navarro', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 28, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento El Hormiguero', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 29, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Felidia', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 30, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Pichindé', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 31, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Los Andes', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 32, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Villacarmelo', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 33, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento La Leonera', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 34, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento La Elvira', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 35, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento Saladito', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 36, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento La Paz', 'zona' => 'RURAL', 'created_at' => now()],
            ['id' => 37, 'tipo' => 'CORREGIMIENTO', 'numero' => null, 'nombre' => 'Corregimiento La Castilla', 'zona' => 'RURAL', 'created_at' => now()],
        ];

        DB::table('comunas_corregimientos')->insert(array_merge($comunas, $corregimientos));

        // 5. Barrios y Veredas
        $barriosVeredas = [
            // Comuna 1
            ['id' => 1, 'comuna_corregimiento_id' => 1, 'tipo' => 'BARRIO', 'nombre' => 'Terrón Colorado', 'codigo_dane' => '760010101', 'latitud_referencia' => 3.4532, 'longitud_referencia' => -76.5562, 'created_at' => now()],
            ['id' => 2, 'comuna_corregimiento_id' => 1, 'tipo' => 'BARRIO', 'nombre' => 'Vista Hermosa', 'codigo_dane' => '760010102', 'latitud_referencia' => 3.4560, 'longitud_referencia' => -76.5610, 'created_at' => now()],
            ['id' => 3, 'comuna_corregimiento_id' => 1, 'tipo' => 'BARRIO', 'nombre' => 'Patio Bonito', 'codigo_dane' => '760010103', 'latitud_referencia' => 3.4510, 'longitud_referencia' => -76.5520, 'created_at' => now()],
            ['id' => 4, 'comuna_corregimiento_id' => 1, 'tipo' => 'BARRIO', 'nombre' => 'Bajo Aguacatal', 'codigo_dane' => '760010104', 'latitud_referencia' => 3.4590, 'longitud_referencia' => -76.5490, 'created_at' => now()],
            // Comuna 2
            ['id' => 5, 'comuna_corregimiento_id' => 2, 'tipo' => 'BARRIO', 'nombre' => 'Granada', 'codigo_dane' => '760010201', 'latitud_referencia' => 3.4580, 'longitud_referencia' => -76.5330, 'created_at' => now()],
            ['id' => 6, 'comuna_corregimiento_id' => 2, 'tipo' => 'BARRIO', 'nombre' => 'Versalles', 'codigo_dane' => '760010202', 'latitud_referencia' => 3.4610, 'longitud_referencia' => -76.5290, 'created_at' => now()],
            ['id' => 7, 'comuna_corregimiento_id' => 2, 'tipo' => 'BARRIO', 'nombre' => 'Santa Mónica', 'codigo_dane' => '760010203', 'latitud_referencia' => 3.4680, 'longitud_referencia' => -76.5260, 'created_at' => now()],
            ['id' => 8, 'comuna_corregimiento_id' => 2, 'tipo' => 'BARRIO', 'nombre' => 'Chipichape', 'codigo_dane' => '760010204', 'latitud_referencia' => 3.4750, 'longitud_referencia' => -76.5280, 'created_at' => now()],
            ['id' => 9, 'comuna_corregimiento_id' => 2, 'tipo' => 'BARRIO', 'nombre' => 'La Flora', 'codigo_dane' => '760010205', 'latitud_referencia' => 3.4830, 'longitud_referencia' => -76.5240, 'created_at' => now()],
            // Comuna 3
            ['id' => 10, 'comuna_corregimiento_id' => 3, 'tipo' => 'BARRIO', 'nombre' => 'San Antonio', 'codigo_dane' => '760010301', 'latitud_referencia' => 3.4470, 'longitud_referencia' => -76.5410, 'created_at' => now()],
            ['id' => 11, 'comuna_corregimiento_id' => 3, 'tipo' => 'BARRIO', 'nombre' => 'El Peñón', 'codigo_dane' => '760010302', 'latitud_referencia' => 3.4505, 'longitud_referencia' => -76.5390, 'created_at' => now()],
            ['id' => 12, 'comuna_corregimiento_id' => 3, 'tipo' => 'BARRIO', 'nombre' => 'San Cayetano', 'codigo_dane' => '760010303', 'latitud_referencia' => 3.4440, 'longitud_referencia' => -76.5360, 'created_at' => now()],
            ['id' => 13, 'comuna_corregimiento_id' => 3, 'tipo' => 'BARRIO', 'nombre' => 'La Merced', 'codigo_dane' => '760010304', 'latitud_referencia' => 3.4530, 'longitud_referencia' => -76.5340, 'created_at' => now()],
            // Comuna 4
            ['id' => 14, 'comuna_corregimiento_id' => 4, 'tipo' => 'BARRIO', 'nombre' => 'Santander', 'codigo_dane' => '760010401', 'latitud_referencia' => 3.4620, 'longitud_referencia' => -76.5150, 'created_at' => now()],
            ['id' => 15, 'comuna_corregimiento_id' => 4, 'tipo' => 'BARRIO', 'nombre' => 'Salomia', 'codigo_dane' => '760010402', 'latitud_referencia' => 3.4690, 'longitud_referencia' => -76.5080, 'created_at' => now()],
            ['id' => 16, 'comuna_corregimiento_id' => 4, 'tipo' => 'BARRIO', 'nombre' => 'Las Delicias', 'codigo_dane' => '760010403', 'latitud_referencia' => 3.4600, 'longitud_referencia' => -76.5190, 'created_at' => now()],
            // Comuna 5
            ['id' => 17, 'comuna_corregimiento_id' => 5, 'tipo' => 'BARRIO', 'nombre' => 'Torres de Comfandi', 'codigo_dane' => '760010501', 'latitud_referencia' => 3.4790, 'longitud_referencia' => -76.5050, 'created_at' => now()],
            ['id' => 18, 'comuna_corregimiento_id' => 5, 'tipo' => 'BARRIO', 'nombre' => 'Los Andes', 'codigo_dane' => '760010502', 'latitud_referencia' => 3.4740, 'longitud_referencia' => -76.5120, 'created_at' => now()],
            ['id' => 19, 'comuna_corregimiento_id' => 5, 'tipo' => 'BARRIO', 'nombre' => 'Paseo de Los Álamos', 'codigo_dane' => '760010503', 'latitud_referencia' => 3.4850, 'longitud_referencia' => -76.5150, 'created_at' => now()],
            // Comuna 6
            ['id' => 20, 'comuna_corregimiento_id' => 6, 'tipo' => 'BARRIO', 'nombre' => 'Los Guaduales', 'codigo_dane' => '760010601', 'latitud_referencia' => 3.4862, 'longitud_referencia' => -76.4950, 'created_at' => now()],
            ['id' => 21, 'comuna_corregimiento_id' => 6, 'tipo' => 'BARRIO', 'nombre' => 'Floralia', 'codigo_dane' => '760010602', 'latitud_referencia' => 3.4910, 'longitud_referencia' => -76.4910, 'created_at' => now()],
            ['id' => 22, 'comuna_corregimiento_id' => 6, 'tipo' => 'BARRIO', 'nombre' => 'Los Alcázares', 'codigo_dane' => '760010603', 'latitud_referencia' => 3.4790, 'longitud_referencia' => -76.4980, 'created_at' => now()],
            ['id' => 23, 'comuna_corregimiento_id' => 6, 'tipo' => 'BARRIO', 'nombre' => 'Paso del Comercio', 'codigo_dane' => '760010604', 'latitud_referencia' => 3.4950, 'longitud_referencia' => -76.4860, 'created_at' => now()],
            ['id' => 24, 'comuna_corregimiento_id' => 6, 'tipo' => 'BARRIO', 'nombre' => 'Petecuy I', 'codigo_dane' => '760010605', 'latitud_referencia' => 3.4820, 'longitud_referencia' => -76.4870, 'created_at' => now()],
            // Comuna 7
            ['id' => 25, 'comuna_corregimiento_id' => 7, 'tipo' => 'BARRIO', 'nombre' => 'Alfonso López I', 'codigo_dane' => '760010701', 'latitud_referencia' => 3.4658, 'longitud_referencia' => -76.4880, 'created_at' => now()],
            ['id' => 26, 'comuna_corregimiento_id' => 7, 'tipo' => 'BARRIO', 'nombre' => 'Puerto Mallarino', 'codigo_dane' => '760010702', 'latitud_referencia' => 3.4610, 'longitud_referencia' => -76.4780, 'created_at' => now()],
            ['id' => 27, 'comuna_corregimiento_id' => 7, 'tipo' => 'BARRIO', 'nombre' => '7 de Agosto', 'codigo_dane' => '760010703', 'latitud_referencia' => 3.4580, 'longitud_referencia' => -76.4950, 'created_at' => now()],
            ['id' => 28, 'comuna_corregimiento_id' => 7, 'tipo' => 'BARRIO', 'nombre' => 'Las Ceibas', 'codigo_dane' => '760010704', 'latitud_referencia' => 3.4680, 'longitud_referencia' => -76.4930, 'created_at' => now()],
            // Comuna 8
            ['id' => 29, 'comuna_corregimiento_id' => 8, 'tipo' => 'BARRIO', 'nombre' => 'La Floresta', 'codigo_dane' => '760010801', 'latitud_referencia' => 3.4490, 'longitud_referencia' => -76.5080, 'created_at' => now()],
            ['id' => 30, 'comuna_corregimiento_id' => 8, 'tipo' => 'BARRIO', 'nombre' => 'Las Américas', 'codigo_dane' => '760010802', 'latitud_referencia' => 3.4440, 'longitud_referencia' => -76.5040, 'created_at' => now()],
            ['id' => 31, 'comuna_corregimiento_id' => 8, 'tipo' => 'BARRIO', 'nombre' => 'Atanasio Girardot', 'codigo_dane' => '760010803', 'latitud_referencia' => 3.4530, 'longitud_referencia' => -76.5110, 'created_at' => now()],
            // Comuna 9
            ['id' => 32, 'comuna_corregimiento_id' => 9, 'tipo' => 'BARRIO', 'nombre' => 'Bretaña', 'codigo_dane' => '760010901', 'latitud_referencia' => 3.4380, 'longitud_referencia' => -76.5310, 'created_at' => now()],
            ['id' => 33, 'comuna_corregimiento_id' => 9, 'tipo' => 'BARRIO', 'nombre' => 'Alameda', 'codigo_dane' => '760010902', 'latitud_referencia' => 3.4420, 'longitud_referencia' => -76.5340, 'created_at' => now()],
            ['id' => 34, 'comuna_corregimiento_id' => 9, 'tipo' => 'BARRIO', 'nombre' => 'Junín', 'codigo_dane' => '760010903', 'latitud_referencia' => 3.4340, 'longitud_referencia' => -76.5270, 'created_at' => now()],
            // Comuna 10
            ['id' => 35, 'comuna_corregimiento_id' => 10, 'tipo' => 'BARRIO', 'nombre' => 'Departamental', 'codigo_dane' => '760011001', 'latitud_referencia' => 3.4210, 'longitud_referencia' => -76.5280, 'created_at' => now()],
            ['id' => 36, 'comuna_corregimiento_id' => 10, 'tipo' => 'BARRIO', 'nombre' => 'San Judas Tadeo', 'codigo_dane' => '760011002', 'latitud_referencia' => 3.4150, 'longitud_referencia' => -76.5220, 'created_at' => now()],
            ['id' => 37, 'comuna_corregimiento_id' => 10, 'tipo' => 'BARRIO', 'nombre' => 'Cristóbal Colón', 'codigo_dane' => '760011003', 'latitud_referencia' => 3.4250, 'longitud_referencia' => -76.5240, 'created_at' => now()],
            // Comuna 11
            ['id' => 38, 'comuna_corregimiento_id' => 11, 'tipo' => 'BARRIO', 'nombre' => '20 de Julio', 'codigo_dane' => '760011101', 'latitud_referencia' => 3.4300, 'longitud_referencia' => -76.5130, 'created_at' => now()],
            ['id' => 39, 'comuna_corregimiento_id' => 11, 'tipo' => 'BARRIO', 'nombre' => 'Prados de Oriente', 'codigo_dane' => '760011102', 'latitud_referencia' => 3.4350, 'longitud_referencia' => -76.5170, 'created_at' => now()],
            ['id' => 40, 'comuna_corregimiento_id' => 11, 'tipo' => 'BARRIO', 'nombre' => 'San Carlos', 'codigo_dane' => '760011103', 'latitud_referencia' => 3.4260, 'longitud_referencia' => -76.5100, 'created_at' => now()],
            // Comuna 12
            ['id' => 41, 'comuna_corregimiento_id' => 12, 'tipo' => 'BARRIO', 'nombre' => 'Nueva Floresta', 'codigo_dane' => '760011201', 'latitud_referencia' => 3.4410, 'longitud_referencia' => -76.4980, 'created_at' => now()],
            ['id' => 42, 'comuna_corregimiento_id' => 12, 'tipo' => 'BARRIO', 'nombre' => 'Asturias', 'codigo_dane' => '760011202', 'latitud_referencia' => 3.4460, 'longitud_referencia' => -76.4950, 'created_at' => now()],
            ['id' => 43, 'comuna_corregimiento_id' => 12, 'tipo' => 'BARRIO', 'nombre' => 'Villanueva', 'codigo_dane' => '760011203', 'latitud_referencia' => 3.4370, 'longitud_referencia' => -76.4920, 'created_at' => now()],
            // Comuna 13
            ['id' => 44, 'comuna_corregimiento_id' => 13, 'tipo' => 'BARRIO', 'nombre' => 'El Poblado II', 'codigo_dane' => '760011301', 'latitud_referencia' => 3.4187, 'longitud_referencia' => -76.4965, 'created_at' => now()],
            ['id' => 45, 'comuna_corregimiento_id' => 13, 'tipo' => 'BARRIO', 'nombre' => 'El Vergel', 'codigo_dane' => '760011302', 'latitud_referencia' => 3.4230, 'longitud_referencia' => -76.5010, 'created_at' => now()],
            ['id' => 46, 'comuna_corregimiento_id' => 13, 'tipo' => 'BARRIO', 'nombre' => 'Los Robles', 'codigo_dane' => '760011303', 'latitud_referencia' => 3.4160, 'longitud_referencia' => -76.4910, 'created_at' => now()],
            ['id' => 47, 'comuna_corregimiento_id' => 13, 'tipo' => 'BARRIO', 'nombre' => 'Calipso', 'codigo_dane' => '760011304', 'latitud_referencia' => 3.4110, 'longitud_referencia' => -76.5050, 'created_at' => now()],
            // Comuna 14
            ['id' => 48, 'comuna_corregimiento_id' => 14, 'tipo' => 'BARRIO', 'nombre' => 'Marroquín II', 'codigo_dane' => '760011401', 'latitud_referencia' => 3.4251, 'longitud_referencia' => -76.4820, 'created_at' => now()],
            ['id' => 49, 'comuna_corregimiento_id' => 14, 'tipo' => 'BARRIO', 'nombre' => 'Los Naranjos', 'codigo_dane' => '760011402', 'latitud_referencia' => 3.4290, 'longitud_referencia' => -76.4800, 'created_at' => now()],
            ['id' => 50, 'comuna_corregimiento_id' => 14, 'tipo' => 'BARRIO', 'nombre' => 'Manuela Beltrán', 'codigo_dane' => '760011403', 'latitud_referencia' => 3.4340, 'longitud_referencia' => -76.4780, 'created_at' => now()],
            ['id' => 51, 'comuna_corregimiento_id' => 14, 'tipo' => 'BARRIO', 'nombre' => 'Las Orquídeas', 'codigo_dane' => '760011404', 'latitud_referencia' => 3.4210, 'longitud_referencia' => -76.4850, 'created_at' => now()],
            // Comuna 15
            ['id' => 52, 'comuna_corregimiento_id' => 15, 'tipo' => 'BARRIO', 'nombre' => 'Morichal de Comfandi', 'codigo_dane' => '760011501', 'latitud_referencia' => 3.4070, 'longitud_referencia' => -76.5120, 'created_at' => now()],
            ['id' => 53, 'comuna_corregimiento_id' => 15, 'tipo' => 'BARRIO', 'nombre' => 'Llano Verde', 'codigo_dane' => '760011502', 'latitud_referencia' => 3.4010, 'longitud_referencia' => -76.5080, 'created_at' => now()],
            ['id' => 54, 'comuna_corregimiento_id' => 15, 'tipo' => 'BARRIO', 'nombre' => 'Ciudad Córdoba', 'codigo_dane' => '760011503', 'latitud_referencia' => 3.4130, 'longitud_referencia' => -76.5160, 'created_at' => now()],
            // Comuna 16
            ['id' => 55, 'comuna_corregimiento_id' => 16, 'tipo' => 'BARRIO', 'nombre' => 'Mariano Ramos', 'codigo_dane' => '760011601', 'latitud_referencia' => 3.4040, 'longitud_referencia' => -76.5200, 'created_at' => now()],
            ['id' => 56, 'comuna_corregimiento_id' => 16, 'tipo' => 'BARRIO', 'nombre' => 'República de Israel', 'codigo_dane' => '760011602', 'latitud_referencia' => 3.4090, 'longitud_referencia' => -76.5240, 'created_at' => now()],
            ['id' => 57, 'comuna_corregimiento_id' => 16, 'tipo' => 'BARRIO', 'nombre' => 'Antonio Nariño', 'codigo_dane' => '760011603', 'latitud_referencia' => 3.4140, 'longitud_referencia' => -76.5210, 'created_at' => now()],
            // Comuna 17
            ['id' => 58, 'comuna_corregimiento_id' => 17, 'tipo' => 'BARRIO', 'nombre' => 'El Ingenio', 'codigo_dane' => '760011701', 'latitud_referencia' => 3.3790, 'longitud_referencia' => -76.5340, 'created_at' => now()],
            ['id' => 59, 'comuna_corregimiento_id' => 17, 'tipo' => 'BARRIO', 'nombre' => 'Valle del Lili', 'codigo_dane' => '760011702', 'latitud_referencia' => 3.3680, 'longitud_referencia' => -76.5280, 'created_at' => now()],
            ['id' => 60, 'comuna_corregimiento_id' => 17, 'tipo' => 'BARRIO', 'nombre' => 'Pasoancho', 'codigo_dane' => '760011703', 'latitud_referencia' => 3.3765, 'longitud_referencia' => -76.5367, 'created_at' => now()],
            ['id' => 61, 'comuna_corregimiento_id' => 17, 'tipo' => 'BARRIO', 'nombre' => 'Limonar', 'codigo_dane' => '760011704', 'latitud_referencia' => 3.3880, 'longitud_referencia' => -76.5390, 'created_at' => now()],
            ['id' => 62, 'comuna_corregimiento_id' => 17, 'tipo' => 'BARRIO', 'nombre' => 'Primero de Mayo', 'codigo_dane' => '760011705', 'latitud_referencia' => 3.3950, 'longitud_referencia' => -76.5360, 'created_at' => now()],
            ['id' => 63, 'comuna_corregimiento_id' => 17, 'tipo' => 'BARRIO', 'nombre' => 'Santa Anita', 'codigo_dane' => '760011706', 'latitud_referencia' => 3.3920, 'longitud_referencia' => -76.5410, 'created_at' => now()],
            // Comuna 18
            ['id' => 64, 'comuna_corregimiento_id' => 18, 'tipo' => 'BARRIO', 'nombre' => 'Meléndez', 'codigo_dane' => '760011801', 'latitud_referencia' => 3.3740, 'longitud_referencia' => -76.5510, 'created_at' => now()],
            ['id' => 65, 'comuna_corregimiento_id' => 18, 'tipo' => 'BARRIO', 'nombre' => 'Polvorines', 'codigo_dane' => '760011802', 'latitud_referencia' => 3.3690, 'longitud_referencia' => -76.5620, 'created_at' => now()],
            ['id' => 66, 'comuna_corregimiento_id' => 18, 'tipo' => 'BARRIO', 'nombre' => 'Los Chorros', 'codigo_dane' => '760011803', 'latitud_referencia' => 3.3820, 'longitud_referencia' => -76.5580, 'created_at' => now()],
            // Comuna 19
            ['id' => 67, 'comuna_corregimiento_id' => 19, 'tipo' => 'BARRIO', 'nombre' => 'San Fernando', 'codigo_dane' => '760011901', 'latitud_referencia' => 3.4280, 'longitud_referencia' => -76.5440, 'created_at' => now()],
            ['id' => 68, 'comuna_corregimiento_id' => 19, 'tipo' => 'BARRIO', 'nombre' => 'Tequendama', 'codigo_dane' => '760011902', 'latitud_referencia' => 3.4170, 'longitud_referencia' => -76.5430, 'created_at' => now()],
            ['id' => 69, 'comuna_corregimiento_id' => 19, 'tipo' => 'BARRIO', 'nombre' => 'Miraflores', 'codigo_dane' => '760011903', 'latitud_referencia' => 3.4350, 'longitud_referencia' => -76.5460, 'created_at' => now()],
            ['id' => 70, 'comuna_corregimiento_id' => 19, 'tipo' => 'BARRIO', 'nombre' => 'El Cedro', 'codigo_dane' => '760011904', 'latitud_referencia' => 3.4310, 'longitud_referencia' => -76.5380, 'created_at' => now()],
            // Comuna 20
            ['id' => 71, 'comuna_corregimiento_id' => 20, 'tipo' => 'BARRIO', 'nombre' => 'Siloé', 'codigo_dane' => '760012001', 'latitud_referencia' => 3.4205, 'longitud_referencia' => -76.5540, 'created_at' => now()],
            ['id' => 72, 'comuna_corregimiento_id' => 20, 'tipo' => 'BARRIO', 'nombre' => 'Tierra Blanca', 'codigo_dane' => '760012002', 'latitud_referencia' => 3.4160, 'longitud_referencia' => -76.5580, 'created_at' => now()],
            ['id' => 73, 'comuna_corregimiento_id' => 20, 'tipo' => 'BARRIO', 'nombre' => 'Belisario Caicedo', 'codigo_dane' => '760012003', 'latitud_referencia' => 3.4230, 'longitud_referencia' => -76.5510, 'created_at' => now()],
            ['id' => 74, 'comuna_corregimiento_id' => 20, 'tipo' => 'BARRIO', 'nombre' => 'La Sultana', 'codigo_dane' => '760012004', 'latitud_referencia' => 3.4260, 'longitud_referencia' => -76.5590, 'created_at' => now()],
            // Comuna 21
            ['id' => 75, 'comuna_corregimiento_id' => 21, 'tipo' => 'BARRIO', 'nombre' => 'Desepaz', 'codigo_dane' => '760012101', 'latitud_referencia' => 3.4320, 'longitud_referencia' => -76.4710, 'created_at' => now()],
            ['id' => 76, 'comuna_corregimiento_id' => 21, 'tipo' => 'BARRIO', 'nombre' => 'Potrero Grande', 'codigo_dane' => '760012102', 'latitud_referencia' => 3.4270, 'longitud_referencia' => -76.4680, 'created_at' => now()],
            ['id' => 77, 'comuna_corregimiento_id' => 21, 'tipo' => 'BARRIO', 'nombre' => 'Compartir', 'codigo_dane' => '760012103', 'latitud_referencia' => 3.4360, 'longitud_referencia' => -76.4740, 'created_at' => now()],
            ['id' => 78, 'comuna_corregimiento_id' => 21, 'tipo' => 'BARRIO', 'nombre' => 'Ciudad Talanga', 'codigo_dane' => '760012104', 'latitud_referencia' => 3.4390, 'longitud_referencia' => -76.4690, 'created_at' => now()],
            // Comuna 22
            ['id' => 79, 'comuna_corregimiento_id' => 22, 'tipo' => 'BARRIO', 'nombre' => 'Ciudad Jardín', 'codigo_dane' => '760012201', 'latitud_referencia' => 3.3610, 'longitud_referencia' => -76.5350, 'created_at' => now()],
            ['id' => 80, 'comuna_corregimiento_id' => 22, 'tipo' => 'BARRIO', 'nombre' => 'Parcelaciones Pance', 'codigo_dane' => '760012202', 'latitud_referencia' => 3.3450, 'longitud_referencia' => -76.5380, 'created_at' => now()],
            ['id' => 81, 'comuna_corregimiento_id' => 22, 'tipo' => 'BARRIO', 'nombre' => 'Campestre', 'codigo_dane' => '760012203', 'latitud_referencia' => 3.3680, 'longitud_referencia' => -76.5420, 'created_at' => now()],
            // Zona Rural: Pance
            ['id' => 82, 'comuna_corregimiento_id' => 23, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Pance Cabecera', 'codigo_dane' => '76001R0101', 'latitud_referencia' => 3.3210, 'longitud_referencia' => -76.5640, 'created_at' => now()],
            ['id' => 83, 'comuna_corregimiento_id' => 23, 'tipo' => 'VEREDA', 'nombre' => 'La Vorágine', 'codigo_dane' => '76001R0102', 'latitud_referencia' => 3.3340, 'longitud_referencia' => -76.5520, 'created_at' => now()],
            ['id' => 84, 'comuna_corregimiento_id' => 23, 'tipo' => 'VEREDA', 'nombre' => 'San Francisco', 'codigo_dane' => '76001R0103', 'latitud_referencia' => 3.3150, 'longitud_referencia' => -76.5780, 'created_at' => now()],
            // La Buitrera
            ['id' => 85, 'comuna_corregimiento_id' => 24, 'tipo' => 'VEREDA', 'nombre' => 'Vereda La Buitrera Cabecera', 'codigo_dane' => '76001R0201', 'latitud_referencia' => 3.3640, 'longitud_referencia' => -76.5720, 'created_at' => now()],
            ['id' => 86, 'comuna_corregimiento_id' => 24, 'tipo' => 'VEREDA', 'nombre' => 'Altos de La Buitrera', 'codigo_dane' => '76001R0202', 'latitud_referencia' => 3.3580, 'longitud_referencia' => -76.5810, 'created_at' => now()],
            // Montebello
            ['id' => 87, 'comuna_corregimiento_id' => 25, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Montebello Centro', 'codigo_dane' => '76001R0301', 'latitud_referencia' => 3.4780, 'longitud_referencia' => -76.5620, 'created_at' => now()],
            // Golondrinas
            ['id' => 88, 'comuna_corregimiento_id' => 26, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Golondrinas Cabecera', 'codigo_dane' => '76001R0401', 'latitud_referencia' => 3.5010, 'longitud_referencia' => -76.5420, 'created_at' => now()],
            // Navarro
            ['id' => 89, 'comuna_corregimiento_id' => 27, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Navarro Cabecera', 'codigo_dane' => '76001R0501', 'latitud_referencia' => 3.3850, 'longitud_referencia' => -76.4820, 'created_at' => now()],
            // El Hormiguero
            ['id' => 90, 'comuna_corregimiento_id' => 28, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Morgan', 'codigo_dane' => '76001R0601', 'latitud_referencia' => 3.3410, 'longitud_referencia' => -76.4780, 'created_at' => now()],
            // Felidia
            ['id' => 91, 'comuna_corregimiento_id' => 29, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Felidia Cabecera', 'codigo_dane' => '76001R0701', 'latitud_referencia' => 3.4730, 'longitud_referencia' => -76.6210, 'created_at' => now()],
            // Pichindé
            ['id' => 92, 'comuna_corregimiento_id' => 30, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Pichindé Cabecera', 'codigo_dane' => '76001R0801', 'latitud_referencia' => 3.4410, 'longitud_referencia' => -76.6180, 'created_at' => now()],
            // Los Andes
            ['id' => 93, 'comuna_corregimiento_id' => 31, 'tipo' => 'VEREDA', 'nombre' => 'Vereda Quebrada Honda', 'codigo_dane' => '76001R0901', 'latitud_referencia' => 3.4210, 'longitud_referencia' => -76.5890, 'created_at' => now()],
            // Villacarmelo
            ['id' => 94, 'comuna_corregimiento_id' => 32, 'tipo' => 'VEREDA', 'nombre' => 'La Gallera', 'codigo_dane' => '76001R1001', 'latitud_referencia' => 3.3780, 'longitud_referencia' => -76.6020, 'created_at' => now()],
            // La Leonera
            ['id' => 95, 'comuna_corregimiento_id' => 33, 'tipo' => 'VEREDA', 'nombre' => 'Vereda La Leonera Cabecera', 'codigo_dane' => '76001R1101', 'latitud_referencia' => 3.4560, 'longitud_referencia' => -76.6390, 'created_at' => now()],
            // La Elvira
            ['id' => 96, 'comuna_corregimiento_id' => 34, 'tipo' => 'VEREDA', 'nombre' => 'Vereda La Elvira Km 18', 'codigo_dane' => '76001R1201', 'latitud_referencia' => 3.5240, 'longitud_referencia' => -76.5890, 'created_at' => now()],
            // Saladito
            ['id' => 97, 'comuna_corregimiento_id' => 35, 'tipo' => 'VEREDA', 'nombre' => 'Vereda El Saladito Cabecera', 'codigo_dane' => '76001R1301', 'latitud_referencia' => 3.4890, 'longitud_referencia' => -76.5920, 'created_at' => now()],
            // La Paz
            ['id' => 98, 'comuna_corregimiento_id' => 36, 'tipo' => 'VEREDA', 'nombre' => 'Vereda La Paz Rural', 'codigo_dane' => '76001R1401', 'latitud_referencia' => 3.5120, 'longitud_referencia' => -76.6110, 'created_at' => now()],
            // La Castilla
            ['id' => 99, 'comuna_corregimiento_id' => 37, 'tipo' => 'VEREDA', 'nombre' => 'Vereda La Castilla Centro', 'codigo_dane' => '76001R1501', 'latitud_referencia' => 3.4980, 'longitud_referencia' => -76.5740, 'created_at' => now()],
        ];
        DB::table('barrios_veredas')->insert($barriosVeredas);

        // 6. Solicitudes de Prueba Iniciales (Para visualización inmediata de Bitácora del Enrutador - Req 4)
        $hoy = Carbon::now();
        $fechaLimite = Carbon::now()->addDays(15)->toDateString();

        $solicitudes = [
            [
                'id' => 1,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0001',
                'nombres_solicitante' => 'Ana Lucía',
                'apellidos_solicitante' => 'Torres Benítez',
                'celular' => '3157894512',
                'direccion' => 'Carrera 83C # 14-22, Apto 301',
                'barrio_id' => 58, // El Ingenio
                'comuna_corregimiento_id' => 17,
                'latitud' => 3.37920000,
                'longitud' => -76.53410000,
                'punto_atencion_id' => 1, // Sede Principal Comuna 17
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subHours(4)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Solicitud para evaluación de estratificación y SISBEN IV en El Ingenio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0002',
                'nombres_solicitante' => 'Héctor Fabio',
                'apellidos_solicitante' => 'Montaño Ruiz',
                'celular' => '3104561234',
                'direccion' => 'Avenida 4A Oeste # 21-15',
                'barrio_id' => 1, // Terrón Colorado
                'comuna_corregimiento_id' => 1,
                'latitud' => 3.45380000,
                'longitud' => -76.55680000,
                'punto_atencion_id' => 2, // CALI 1
                'usuario_ventanilla_id' => 3,
                'fecha_radicacion' => $hoy->copy()->subHours(3)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Vivienda unifamiliar sector Terrón Colorado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0003',
                'nombres_solicitante' => 'Gloria Inés',
                'apellidos_solicitante' => 'Valencia Cardona',
                'celular' => '3189901122',
                'direccion' => 'Calle 70A # 8N-45',
                'barrio_id' => 20, // Los Guaduales
                'comuna_corregimiento_id' => 6,
                'latitud' => 3.48650000,
                'longitud' => -76.49520000,
                'punto_atencion_id' => 3, // CALI 6
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subHours(3)->addMinutes(15)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Madre cabeza de hogar requiere encuesta prioritaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0004',
                'nombres_solicitante' => 'Jorge Eliécer',
                'apellidos_solicitante' => 'Giraldo Soto',
                'celular' => '3123456789',
                'direccion' => 'Carrera 7G # 74-12',
                'barrio_id' => 25, // Alfonso López I
                'comuna_corregimiento_id' => 7,
                'latitud' => 3.46600000,
                'longitud' => -76.48830000,
                'punto_atencion_id' => 4, // CALI 7
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subHours(2)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Alfonso López I sector puente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0005',
                'nombres_solicitante' => 'Martha Cecilia',
                'apellidos_solicitante' => 'Díaz Ortiz',
                'celular' => '3166543210',
                'direccion' => 'Calle 72Q # 28E-18',
                'barrio_id' => 44, // El Poblado II
                'comuna_corregimiento_id' => 13,
                'latitud' => 3.41890000,
                'longitud' => -76.49680000,
                'punto_atencion_id' => 5, // CALI 13
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subHours(2)->addMinutes(20)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Poblado II cerca a polideportivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0006',
                'nombres_solicitante' => 'Jhon Jairo',
                'apellidos_solicitante' => 'Mina Carabalí',
                'celular' => '3171122334',
                'direccion' => 'Carrera 27A # 79-30',
                'barrio_id' => 48, // Marroquín II
                'comuna_corregimiento_id' => 14,
                'latitud' => 3.42530000,
                'longitud' => -76.48220000,
                'punto_atencion_id' => 6, // CALI 14
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subHours(1)->addMinutes(10)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Marroquín II sector oriente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0007',
                'nombres_solicitante' => 'Sandra Milena',
                'apellidos_solicitante' => 'Osorio Vega',
                'celular' => '3145566778',
                'direccion' => 'Carrera 52A # 3-10',
                'barrio_id' => 71, // Siloé
                'comuna_corregimiento_id' => 20,
                'latitud' => 3.42080000,
                'longitud' => -76.55430000,
                'punto_atencion_id' => 7, // CALI 20
                'usuario_ventanilla_id' => 3,
                'fecha_radicacion' => $hoy->copy()->subMinutes(50)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Siloé sector La Estrella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0008',
                'nombres_solicitante' => 'Diego Fernando',
                'apellidos_solicitante' => 'Palacios Lerma',
                'celular' => '3139988776',
                'direccion' => 'Calle 121 # 23-45',
                'barrio_id' => 75, // Desepaz
                'comuna_corregimiento_id' => 21,
                'latitud' => 3.43220000,
                'longitud' => -76.47130000,
                'punto_atencion_id' => 8, // CALI 21
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subMinutes(30)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Desepaz etapa 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'radicado_codigo' => 'SOL-' . $hoy->format('Ymd') . '-0009',
                'nombres_solicitante' => 'Luz Marina',
                'apellidos_solicitante' => 'Gómez de Pance',
                'celular' => '3117766554',
                'direccion' => 'Km 12 Vía La Vorágine, Finca El Recuerdo',
                'barrio_id' => 83, // La Vorágine (Pance)
                'comuna_corregimiento_id' => 23, // Corregimiento Pance
                'latitud' => 3.33420000,
                'longitud' => -76.55240000,
                'punto_atencion_id' => 1, // Radicado en Sede Principal
                'usuario_ventanilla_id' => 2,
                'fecha_radicacion' => $hoy->copy()->subMinutes(15)->toDateTimeString(),
                'fecha_limite' => $fechaLimite,
                'estado' => 'RADICADA',
                'observaciones' => 'Zona rural Pance sector turístico La Vorágine',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('solicitudes_encuesta')->insert($solicitudes);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }
}
