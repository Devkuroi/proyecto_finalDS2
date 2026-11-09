================================================================================
SISTEMA DE GESTION Y GEORREFERENCIACION DE ENCUESTAS - SANTIAGO DE CALI
ENTREGA 1 - REQUERIMIENTOS 1, 2, 3, 4 + PRECARGA DE SEDES (REQ 5)
================================================================================

1. DESCRIPCION DEL PROYECTO
--------------------------------------------------------------------------------
Sistema web institucional tipo RIA (Rich Internet Application) desarrollado
para la gestion, radicacion georreferenciada y control de bitacora diaria
de solicitudes de encuestas domiciliarias en el municipio de Santiago de Cali,
cubriendo sus 22 Comunas urbanas y sus 15 Corregimientos rurales oficiales.

Alcance Estricto Entrega 1:
- Requerimiento 1: Autenticacion institucional de los 5 perfiles del sistema.
- Requerimiento 2: Catalogo territorial normalizado de Cali (Comunas y Barrios).
- Requerimiento 3: Radicacion y georreferenciacion interactiva con Leaflet.js.
- Requerimiento 4: Bitacora diaria del Enrutador consolidando sedes y politica de 15 dias.
- Requerimiento 5 (Precarga): 8 Sedes oficiales de atencion (Comunas 1, 6, 7, 13, 14, 17 Principal, 20 y 21).

NOTA DE ALCANCE:
Los Requerimientos 6 (Hojas de ruta de 20 encuestas), 7 (Entrega de rutas al
encuestador), 8 (Reportes PDF) y 9 (Dashboard analitico) pertenecen al Sprint 2.
NO estan programados en esta entrega conforme a la restriccion estricta de alcance.


2. STACK TECNOLOGICO
--------------------------------------------------------------------------------
- Backend: PHP 8.2+ con Laravel 11/12 (Arquitectura MVC limpia con PDO seguro)
- Base de Datos: MySQL 8.0 (con soporte SQLite en testing automatizado)
- Frontend: Blade + Tailwind CSS + Alpine.js (Fetch API asincrono tipo RIA)
- GIS y Mapas: Leaflet.js 1.9.4 + OpenStreetMap (Centrado oficial: lat 3.4516, lng -76.5320)
- DevOps: Docker, Docker Compose y GitHub Actions (.github/workflows/ci.yml)


3. GUIA DE EJECUCION RAPIDA CON DOCKER (RECOMENDADA)
--------------------------------------------------------------------------------
Requisitos: Docker Desktop y Docker Compose instalados y en ejecucion.

Paso 1: Abrir una terminal en la raiz del proyecto:
        cd C:\Users\...\proyecto_finalDS2

Paso 2: Construir y levantar los contenedores:
        docker compose up --build

Paso 3: Abrir su navegador web en:
        http://localhost:8000

El contenedor levantara:
- Contenedor 'db' (MySQL 8.0 en puerto 3306) con precarga automatica de
  los scripts database/sql/schema.sql y database/sql/data.sql.
- Contenedor 'app' (PHP 8.2 + Apache en puerto 8000).


4. GUIA DE EJECUCION MANUAL LOCAL (SIN DOCKER)
--------------------------------------------------------------------------------
Requisitos: PHP 8.2 o superior, Composer y extensiones pdo, pdo_sqlite/pdo_mysql.

Paso 1: Instalar dependencias de Composer:
        composer install

Paso 2: Generar clave institucional:
        php artisan key:generate

Paso 3: Ejecutar migraciones y precarga de seeders:
        php artisan migrate:fresh --seed

Paso 4: Iniciar el servidor web de desarrollo:
        php artisan serve --port=8000

Paso 5: Acceder en el navegador a:
        http://127.0.0.1:8000


5. TABLA DE USUARIOS DE PRUEBA Y CREDENCIALES (MODO DEMO 1-CLIC)
--------------------------------------------------------------------------------
En la pantalla de Login (http://localhost:8000/login) encontrara una barra lateral
con botones de "MODO DEMOSTRACION (1 CLIC)" para que el evaluador pueda ingresar
instantaneamente con cualquiera de los 5 roles exigidos sin digitar contrasenas:

+---------------+--------------------------+---------------+-----------------------+
| ROL           | CORREO ELECTRONICO       | CONTRASENA    | SEDE ASIGNADA         |
+---------------+--------------------------+---------------+-----------------------+
| VENTANILLA    | ventanilla17@cali.gov.co | Password123!  | Sede Principal (C17)  |
| VENTANILLA    | ventanilla1@cali.gov.co  | Password123!  | Punto Atencion (C1)   |
| ENRUTADOR     | enrutador@cali.gov.co    | Password123!  | Oficina Central       |
| ADMINISTRADOR | admin@cali.gov.co        | Password123!  | Oficina Central       |
| ENCUESTADOR   | encuestador1@cali.gov.co | Password123!  | Campo / Itinerante    |
| CALIDAD       | calidad@cali.gov.co      | Password123!  | Auditoria Central     |
+---------------+--------------------------+---------------+-----------------------+

Contrasena universal para todos los usuarios: Password123!


6. EJECUCION DE LA SUITE DE PRUEBAS AUTOMATIZADAS (PHPUNIT)
--------------------------------------------------------------------------------
Para ejecutar las pruebas funcionales de los Requerimientos 1, 2, 3 y 4:

        php artisan test

Resultado: 15 tests aprobados (80 aserciones), cubriendo:
- Login exitoso de los 5 roles y rechazo de credenciales erroneas.
- Listado de 22 comunas y 15 corregimientos de Cali.
- Filtro estricto de barrios por comuna (integridad territorial).
- Listado de las 8 sedes oficiales en comunas 1, 6, 7, 13, 14, 17, 20 y 21.
- Radicacion georreferenciada con calculo automatico de +15 dias (politica).
- Bloqueo y rechazo 422 si el barrio no pertenece a la comuna elegida.
- Bitacora diaria del enrutador con consolidados metricos.
- Control de acceso por roles y proteccion de endpoints.


7. ESTRUCTURA DE ARCHIVOS PRINCIPALES DEL PROYECTO
--------------------------------------------------------------------------------
proyecto_finalDS2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          (Req 1: Login, Logout, Me)
│   │   │   ├── VentanillaController.php    (Req 2 & 3: Radicacion + GIS)
│   │   │   ├── EnrutadorController.php     (Req 4: Bitacora Diaria Consolidada)
│   │   │   ├── TerritorioController.php    (Catalogo Comunas/Barrios/Sedes)
│   │   │   └── DashboardController.php     (Placeholders de perfiles Entrega 2)
│   │   └── Middleware/
│   │       └── CheckRole.php               (Autorizacion institucional por rol)
│   └── Models/
│       ├── User.php                        (Tabla 'usuarios')
│       ├── Rol.php                         (Tabla 'roles')
│       ├── PuntoAtencion.php               (Tabla 'puntos_atencion')
│       ├── ComunaCorregimiento.php         (Tabla 'comunas_corregimientos')
│       ├── BarrioVereda.php                (Tabla 'barrios_veredas')
│       └── SolicitudEncuesta.php           (Tabla 'solicitudes_encuesta')
├── database/
│   ├── migrations/                         (Esquema Laravel reproducible)
│   ├── seeders/                            (Datos maestros y solicitudes prueba)
│   └── sql/
│       ├── schema.sql                      (Esquema DDL puro MySQL 8.0)
│       └── data.sql                        (Datos DML puros MySQL 8.0)
├── resources/views/
│   ├── layouts/app.blade.php               (Plantilla maestra institucional)
│   ├── auth/login.blade.php                (Login con Demo Mode 1-clic)
│   ├── ventanilla/index.blade.php          (Formulario RIA + Mapa Leaflet Cali)
│   ├── enrutador/bitacora.blade.php        (Bitacora diaria + Mapa consolidado)
│   └── dashboard/placeholder.blade.php     (Vistas activas perfiles Entrega 2)
├── tests/
│   └── Feature/
│       ├── Entrega1BackendTest.php         (Pruebas de endpoints Req 1 al 4)
│       └── Entrega1ViewsTest.php           (Pruebas de renderizado de vistas)
├── docker/
│   ├── entrypoint.sh                       (Script de arranque de contenedor)
│   └── vhost.conf                          (Configuracion de Apache DocumentRoot)
├── .github/workflows/
│   └── ci.yml                              (Pipeline GitHub Actions IC/EC)
├── Dockerfile                              (Definicion de imagen de aplicacion)
├── docker-compose.yml                      (Orquestacion App + MySQL 8.0)
├── docs/entrega1/
│   └── INFORME_ENTREGA_1.md                (Informe Academico Completo 35%)
└── readme.txt                              (Instrucciones de operacion y entrega)

================================================================================
Desarrollado con estandares de Ingenieria de Software y Scrum.
Santiago de Cali, 2026.
================================================================================
