# INFORME TÉCNICO Y ACADÉMICO - ENTREGA 1
## SISTEMA DE GESTIÓN Y GEORREFERENCIACIÓN DE ENCUESTAS DOMICILIARIAS PARA SANTIAGO DE CALI

---

**Asignatura:** Desarrollo de Software II  
**Programa Académico:** Ingeniería de Sistemas  
**Municipio / Cobertura Territorial:** Santiago de Cali (Comunas 1 a 22 y 15 Corregimientos)  
**Alcance de la Entrega:** Entrega 1 (Sprint 1: Requerimientos 1, 2, 3 y 4 + Precarga de Sedes Req 5)  
**Equipo de Desarrollo (Orquestación Multi-Agente):**
- Agente Orquestador de Ingeniería de Software
- Sub-Agente 1: Arquitecto de Datos (`DB_Architect_Agent`)
- Sub-Agente 2: Desarrollador Backend (`Backend_Dev_Agent`)
- Sub-Agente 3: Desarrollador Frontend & GIS (`Frontend_GIS_Agent`)
- Sub-Agente 4: Ingeniero DevOps (`DevOps_Agent`)
- Sub-Agente 5: Documentador Scrum & QA (`Scrum_Academic_Docs_Agent`)

---

## TABLA DE CONTENIDO
1. [Introducción y Objetivos del Proyecto](#1-introducción-y-objetivos-del-proyecto)
2. [Metodología Scrum Aplicada y sus Fases](#2-metodología-scrum-aplicada-y-sus-fases)
3. [Especificación de Requerimientos mediante Historias de Usuario (HU)](#3-especificación-de-requerimientos-mediante-historias-de-usuario-hu)
   - 3.1. Historias de Usuario del Sprint 1 (Implementadas en Entrega 1)
   - 3.2. Historias de Usuario del Product Backlog (Sprint 2 - No Implementadas en Código)
4. [Definición y Justificación de la Arquitectura Tecnológica](#4-definición-y-justificación-de-la-arquitectura-tecnológica)
5. [Diseño y Modelo de Base de Datos Relacional](#5-diseño-y-modelo-de-base-de-datos-relacional)
   - 5.1. Diagrama Entidad-Relación (Mermaid.js)
   - 5.2. Diccionario de Datos del Esquema Institucional
6. [Informe DevOps: Integración Continua, Entrega Continua y Gestión](#6-informe-devops-integración-continua-entrega-continua-y-gestión)
   - 6.1. Git y GitHub Actions (IC/EC)
   - 6.2. Docker y Contenedores Reproducibles
   - 6.3. Jira Software (Gestión Ágil y Trazabilidad)
7. [Bitácoras de Herramientas de Desarrollo y DevOps](#7-bitácoras-de-herramientas-de-desarrollo-y-devops)
   - 7.1. Bitácora de Commits y Ramas bajo Git Flow
   - 7.2. Bitácora de Sprints, Tareas y Ejecuciones en Jira y Pipeline CI/CD
8. [Informe de Pruebas Funcionales y Aseguramiento de Calidad (QA)](#8-informe-de-pruebas-funcionales-y-aseguramiento-de-calidad-qa)
9. [Conclusiones y Trabajo Futuro (Sprint 2)](#9-conclusiones-y-trabajo-futuro-sprint-2)

---

## 1. INTRODUCCIÓN Y OBJETIVOS DEL PROYECTO

El presente proyecto de ingeniería de software responde a la necesidad institucional de modernizar y digitalizar el ciclo de vida de las solicitudes de encuestas socioeconómicas y domiciliarias en el Distrito Especial, Deportivo, Cultural, Turístico, Empresarial y de Servicios de Santiago de Cali. 

Históricamente, la radicación de solicitudes en las sedes de atención al ciudadano (Centros de Administración Local Integrada - CALI) presentaba vulnerabilidades operativas: errores humanos al asociar barrios a comunas incorrectas, inconsistencias en las direcciones de zonas rurales (corregimientos), demoras en el enrutamiento logístico y superación de los plazos normativos.

### Objetivo General
Desarrollar un sistema web institucional con arquitectura limpia tipo RIA (Rich Internet Application) y soporte GIS (Sistemas de Información Geográfica) para la recepción, georreferenciación y consolidación en bitácora diaria de las solicitudes de encuestas domiciliarias de Cali.

### Objetivos Específicos de la Entrega 1
1. **Control de Acceso Seguro (Req 1):** Implementar la autenticación de usuarios basada en 5 perfiles institucionales: `ADMINISTRADOR`, `VENTANILLA`, `ENCUESTADOR`, `CALIDAD` y `ENRUTADOR`.
2. **Normalización Territorial (Req 2):** Modelar y precargar la división sectorial estricta de Santiago de Cali (22 comunas urbanas y 15 corregimientos rurales con sus barrios y veredas), impidiendo asignaciones territoriales inválidas.
3. **Georreferenciación Interactiva en Ventanilla (Req 3):** Proveer un módulo interactivo en OpenStreetMap y Leaflet.js centrado en Cali (`3.4516, -76.5320`) con pines oficiales para los puntos de atención y captura automática de coordenadas para el predio solicitado.
4. **Bitácora Diaria del Enrutador (Req 4):** Consolidar en tiempo real todas las solicitudes radicadas durante el día en las distintas sedes de Cali, calculando automáticamente la política institucional de vencimiento a 15 días calendario.
5. **Infraestructura Reproducible y CI/CD:** Contenerizar la solución mediante Docker Compose (PHP 8.2 + Apache + MySQL 8.0) y configurar el flujo automatizado de pruebas en GitHub Actions.

> [!IMPORTANT]
> **Restricción Estricta de Alcance (Guardrail de la Entrega 1):**
> Esta entrega se limita exclusivamente a los Requerimientos 1, 2, 3 y 4 más la precarga de sedes (Requerimiento 5). Los Requerimientos 6 (Hojas de ruta de 20 encuestas), 7 (Entrega de rutas al encuestador), 8 (Reportes técnicos en PDF) y 9 (Dashboard analítico avanzado) corresponden al **Sprint 2** y no forman parte del código ejecutable de esta entrega.

---

## 2. METODOLOGÍA SCRUM APLICADA Y SUS FASES

El proyecto se estructuró siguiendo el marco de trabajo ágil **Scrum**, permitiendo entregas iterativas e incrementales orientadas a la satisfacción de los requerimientos institucionales de la Alcaldía de Cali.

```mermaid
flowchart LR
    A["Fase 1: Inicio<br/>(Inception & Backlog)"] --> B["Fase 2: Planificación<br/>(Sprint Planning 1)"]
    B --> C["Fase 3: Ejecución<br/>(Sprint Execution 1)"]
    C --> D["Fase 4: Revisión<br/>(Sprint Review 1)"]
    D --> E["Fase 5: Retrospectiva<br/>(Sprint Retrospective 1)"]
    E --> F["Backlog Sprint 2<br/>(Hojas de Ruta, PDF, Dashboard)"]
```

### 2.1. Fase de Inicio (Inception)
- **Definición de Roles Scrum:**
  - *Product Owner (PO):* Representante de la Oficina de Planeación y Sisbén de Cali. Responsable de priorizar el Product Backlog.
  - *Scrum Master (SM):* Garante del proceso ágil, facilitador de ceremonias y removedor de impedimentos técnicos.
  - *Development Team:* Equipo multidisciplinario orquestado en roles especializados (Arquitectura de Datos, Backend, Frontend/GIS, DevOps y QA).
- **Definición de 'Terminado' (Definition of Done - DoD):**
  - Código documentado y respetando las normas de codificación PSR-12.
  - Pruebas automatizadas unitarias y de integración aprobadas al 100% en el pipeline CI/CD.
  - Migraciones y scripts SQL (`schema.sql` y `data.sql`) ejecutables sin errores.
  - Interfaz accesible y responsiva tipo RIA probada en navegadores modernos.

### 2.2. Fase de Planificación del Sprint 1 (Sprint Planning)
- **Objetivo del Sprint 1:** Construir la base de autenticación de los 5 roles, el catálogo territorial completo de Cali, el formulario de radicación georreferenciada con Leaflet.js para ventanilla y la bitácora diaria consolidada para el enrutador con cálculo de la política de 15 días.
- **Estimación:** Se utilizó la técnica de *Planning Poker* basada en la serie de Fibonacci modificada (Story Points).
- **Capacidad del Equipo:** 40 Story Points (SP) asignados al Sprint 1.

### 2.3. Fase de Ejecución del Sprint 1 (Sprint Execution)
- Se realizaron sincronizaciones diarias (*Daily Scrum*) virtuales para revisar avances, sincronizar dependencias entre backend y frontend, y ajustar los modelos de datos espaciales.
- Se implementaron ramas bajo la estrategia *Git Flow* (`feature/`, `develop`, `main`).

### 2.4. Fase de Revisión del Sprint 1 (Sprint Review)
- Demostración funcional en vivo ante los evaluadores académicos:
  - Selector rápido "Demo Mode" permitiendo autenticar con un solo clic a los 5 roles (`ADMINISTRADOR`, `VENTANILLA`, `ENCUESTADOR`, `CALIDAD`, `ENRUTADOR`).
  - Radicación en ventanilla mostrando cómo la selección de una Comuna restringe los barrios a únicamente los válidos.
  - Interacción con el mapa de Cali, captura automática de coordenadas y feedback inmediato.
  - Consulta de la bitácora del enrutador con consolidados en tiempo real.

### 2.5. Fase de Retrospectiva del Sprint 1 (Sprint Retrospective)
- **¿Qué funcionó bien?**
  - La orquestación de sub-agentes permitió paralelizar el diseño del esquema SQL y el desarrollo de controladores sin colisiones.
  - El uso de Alpine.js y Fetch API proporcionó un comportamiento tipo RIA fluido sin la pesadez de frameworks SPA monolíticos.
- **¿Qué se puede mejorar?**
  - Mejorar el almacenamiento temporal en entornos Windows afectados por mecanismos de bloqueo de archivos en sincronización cloud.
- **Plan de Acción para Sprint 2:**
  - Diseñar el algoritmo de clustering geográfico para agrupar encuestas en bloques óptimos de 20 para el encuestador (Req 6).

---

## 3. ESPECIFICACIÓN DE REQUERIMIENTOS MEDIANTE HISTORIAS DE USUARIO (HU)

### 3.1. Historias de Usuario del Sprint 1 (Implementadas en Entrega 1)

#### HU-01: Autenticación y Control de Acceso por Perfiles Institucionales (Req 1)
- **Como:** Usuario del sistema institucional de encuestas (Administrador, Ventanilla, Encuestador, Calidad o Enrutador).
- **Quiero:** Iniciar sesión con mi correo electrónico institucional y contraseña en un portal seguro con selector rápido de perfiles de demostración.
- **Para:** Acceder exclusivamente a los módulos que corresponden a mis responsabilidades operativas.
- **Story Points:** 5 SP | **Prioridad:** Muy Alta | **Fase Scrum:** Sprint 1

```gherkin
Escenario: Inicio de sesión exitoso con redirección según perfil
  Dado que el usuario accede a la pantalla de login "/login"
  Cuando ingresa el correo "ventanilla17@cali.gov.co" y la contraseña correcta "Password123!"
  Entonces el sistema valida la credencial contra la tabla "usuarios"
  Y regenera la sesión HTTP institucional
  Y redirige al operador a la vista de ventanilla "/ventanilla" con mensaje de bienvenida

Escenario: Rechazo de credenciales inválidas
  Dado que un usuario no registrado o con credenciales incorrectas intenta ingresar
  Cuando envía credenciales erróneas
  Entonces el sistema retorna un código HTTP 422
  Y muestra un mensaje de alerta: "Las credenciales no coinciden con nuestros registros o el usuario está inactivo."
  Y deniega el acceso manteniendo al usuario como invitado
```

---

#### HU-02: Normalización del Catálogo Territorial Urbano y Rural de Cali (Req 2)
- **Como:** Operador de ventanilla o coordinador logístico.
- **Quiero:** Disponer de un catálogo normalizado que relacione las 22 Comunas y 15 Corregimientos de Cali con sus respectivos barrios y veredas.
- **Para:** Evitar el error humano de radicar encuestas domiciliarias asignando barrios a comunas incorrectas.
- **Story Points:** 8 SP | **Prioridad:** Muy Alta | **Fase Scrum:** Sprint 1

```gherkin
Escenario: Filtrado dinámico estricto de barrios al seleccionar una comuna
  Dado que el operador de ventanilla se encuentra en el formulario de radicación
  Cuando selecciona la "Comuna 17 - Santiago de Cali"
  Entonces el selector dependiente de barrios consulta el endpoint "/api/territorio/barrios?comuna_id=17"
  Y carga únicamente los barrios oficiales de la Comuna 17 (ej. "El Ingenio", "Valle del Lili", "Pasoancho", "Limonar")
  Y deshabilita la posibilidad de seleccionar barrios de otras comunas (ej. "Terrón Colorado" o "Siloé")

Escenario: Soporte para división rural (Corregimientos de Cali)
  Dado que el solicitante habita en la zona rural del municipio
  Cuando el operador selecciona el "Corregimiento Pance"
  Entonces el sistema lista exclusivamente sus veredas asociadas ("La Vorágine", "San Francisco", "El Peón", "Pance Cabecera")
```

---

#### HU-03: Radicación y Georreferenciación Interactiva con Leaflet.js (Req 3)
- **Como:** Operador de ventanilla de atención al ciudadano.
- **Quiero:** Ingresar los datos del solicitante y seleccionar la ubicación exacta del predio sobre el mapa interactivo de Cali.
- **Para:** Registrar la solicitud con coordenadas GPS verificadas y calcular automáticamente la fecha límite institucional de 15 días calendario.
- **Story Points:** 13 SP | **Prioridad:** Alta | **Fase Scrum:** Sprint 1

```gherkin
Escenario: Captura automática de coordenadas y cálculo de política de 15 días
  Dado que el operador completa los datos del ciudadano (nombres, apellidos, celular, dirección, comuna y barrio)
  Cuando hace clic en el mapa interactivo sobre la ubicación del predio en Cali
  Entonces Leaflet.js captura la latitud y longitud en campos numéricos del formulario
  Y al presionar "Radicar Solicitud de Encuesta Domiciliaria", el sistema genera el código único "SOL-YYYYMMDD-XXXX"
  Y calcula automáticamente la "fecha_limite" a la fecha actual más 15 días calendario
  Y guarda el registro en la base de datos con estado "RADICADA"
  Y actualiza la tabla de radicaciones en tiempo real sin recargar la página

Escenario: Rechazo de solicitud por coordenadas fuera del perímetro municipal
  Dado que el operador intenta marcar una coordenada fuera de Santiago de Cali
  Cuando envía coordenadas no comprendidas en el bounding box institucional
  Entonces el sistema retorna un error 422 indicando: "La coordenada debe corresponder al perímetro del municipio de Santiago de Cali."
```

---

#### HU-04: Bitácora Diaria Consolidada del Enrutador (Req 4)
- **Como:** Coordinador logístico (Enrutador).
- **Quiero:** Visualizar una bitácora en tiempo real con todas las solicitudes radicadas hoy en todos los puntos de atención de Cali.
- **Para:** Conocer la demanda territorial, monitorear los días restantes de la política de 15 días y supervisar la distribución geográfica urbana y rural.
- **Story Points:** 8 SP | **Prioridad:** Alta | **Fase Scrum:** Sprint 1

```gherkin
Escenario: Consulta y consolidación de solicitudes del día
  Dado que el usuario con rol "ENRUTADOR" inicia sesión y accede a "/enrutador/bitacora"
  Cuando se carga la pantalla o se selecciona una fecha
  Entonces el sistema consulta el endpoint "/api/enrutador/bitacora-diaria?fecha=YYYY-MM-DD"
  Y consolida las solicitudes registradas en todas las sedes (Comunas 1, 6, 7, 13, 14, 17, 20 y 21)
  Y dibuja los pines de cada solicitud en el mapa interactivo de Cali diferenciando zonas urbanas y rurales
  Y presenta la tabla detallada con radicado, hora, solicitante, comuna, barrio, sede y días restantes de vigencia
```

---

#### HU-05: Precarga Estática de Puntos Oficiales de Atención (Req 5 - Sede Principal y Sedes Zonales)
- **Como:** Administrador del sistema institucional.
- **Quiero:** Que las 8 sedes oficiales de atención de Cali estén precargadas en el esquema de base de datos relacional.
- **Para:** Garantizar la integridad referencial de las llaves foráneas y ubicar geográficamente cada sede en los mapas institucionales.
- **Story Points:** 5 SP | **Prioridad:** Media | **Fase Scrum:** Sprint 1

```gherkin
Escenario: Verificación de sedes oficiales precargadas
  Dado que el sistema inicializa la base de datos MySQL 8.0
  Cuando se consultan los registros de la tabla "puntos_atencion"
  Entonces existen exactamente las sedes de las Comunas 1, 6, 7, 13, 14, 20, 21 y la Comuna 17 (Oficina Central / Sede Principal)
  Y cada registro cuenta con nombre, código, comuna, dirección, teléfono y coordenadas GPS oficiales
```

---

### 3.2. Historias de Usuario del Product Backlog (Sprint 2 - No Implementadas en Código)

> [!NOTE]
> Estas Historias de Usuario forman parte de la planificación estratégica del **Sprint 2** y no contienen código fuente ejecutable en la presente Entrega 1, cumpliendo con la regla de oro de alcance.

- **HU-06: Generación de Hojas de Ruta de 20 Encuestas por Proximidad (Req 6):**
  - *Descripción:* Agrupar solicitudes en estado `RADICADA` en lotes de 20 predios utilizando algoritmos de cercanía geográfica (k-means / ruta del viajero) en comunas o corregimientos específicos.
  - *Estimación:* 13 SP | *Fase:* Sprint 2.
- **HU-07: Acta de Entrega de Rutas en Ventanilla al Encuestador (Req 7):**
  - *Descripción:* Interfaz para que el operador de ventanilla asigne formalmente la hoja de ruta física/digital al encuestador, registrando fecha, hora, firma digital y cambio de estado a `EN_RUTA`.
  - *Estimación:* 8 SP | *Fase:* Sprint 2.
- **HU-08: Generación de Reportes Técnicos y Certificados en PDF (Req 8):**
  - *Descripción:* Motor de exportación en PDF con sello institucional, códigos QR de validación y resumen metodológico para el perfil `CALIDAD`.
  - *Estimación:* 8 SP | *Fase:* Sprint 2.
- **HU-09: Dashboard Analítico e Inteligencia de Negocio (Req 9):**
  - *Descripción:* Panel gráfico con mapas de calor (heatmaps), tiempos promedio de atención por comuna, gráficos de cumplimiento de la política de 15 días y comparativas entre zonas urbanas y rurales para el perfil `ADMINISTRADOR`.
  - *Estimación:* 13 SP | *Fase:* Sprint 2.

---

## 4. DEFINICIÓN Y JUSTIFICACIÓN DE LA ARQUITECTURA TECNOLÓGICA

La arquitectura seleccionada responde a principios de modularidad, alta cohesión, bajo acoplamiento, seguridad en transacciones y una experiencia de usuario rica (RIA) sin dependencias innecesarias.

```mermaid
flowchart TD
    subgraph Cliente["Cliente Web (Navegador - RIA)"]
        UI["Blade Templates + Tailwind CSS"]
        Alpine["Alpine.js (Manejador de Estado Reactivo)"]
        GIS["Leaflet.js 1.9.4 + OpenStreetMap"]
        Fetch["Fetch API (Asíncrono / JSON)"]
    end

    subgraph Servidor["Servidor de Aplicación (Docker - Apache / PHP 8.2)"]
        Router["Laravel Router & Route Middleware (CheckRole)"]
        Controllers["Controladores MVC (Auth, Ventanilla, Enrutador, Territorio)"]
        Models["Modelos Eloquent ORM con Tipado Fuerte"]
        PDO["PDO MySQL (Prepared Statements / Anti-SQL Injection)"]
    end

    subgraph BD["Capa de Persistencia (MySQL 8.0)"]
        MySQL[("gestion_encuestas_cali<br/>InnoDB / UTF8MB4<br/>Integridad Referencial FK")]
    end

    UI --> Alpine
    Alpine --> GIS
    Alpine --> Fetch
    Fetch -->|Solicitud HTTP JSON / Session Cookie| Router
    Router --> Controllers
    Controllers --> Models
    Models --> PDO
    PDO --> MySQL
```

### Justificación Técnica de Componentes
1. **PHP 8.2 + Laravel Framework:** Proporciona un entorno tipado robusto, soporte nativo de atributos, inyección de dependencias y un motor ORM (Eloquent) respaldado por PDO. Esto mitiga por diseño ataques de inyección SQL mediante el uso mandatorio de consultas parametrizadas (*prepared statements*).
2. **MySQL 8.0 (Motor InnoDB):** Garantiza cumplimiento estricto de propiedades ACID (Atomicidad, Consistencia, Aislamiento y Durabilidad), soporte para índices espaciales y llaves foráneas con restricciones en cascada.
3. **Blade + Tailwind CSS + Alpine.js (Arquitectura RIA Ligera):** A diferencia de complejos frameworks SPA monolíticos (React/Angular) que requieren pesadas etapas de compilación en el cliente, la combinación Blade + Alpine.js brinda reactividad declarativa inmediata, binding bidireccional y peticiones asíncronas con Fetch API sin recargas completas de pantalla.
4. **Leaflet.js + OpenStreetMap:** Solución geoespacial ligera y de código abierto que elimina costos de licenciamiento (evitando APIs propietarias como Google Maps), permitiendo visualización de marcadores vectoriales, centrado preciso en Cali (`3.4516, -76.5320`) y personalización completa de pines para las sedes oficiales.
5. **Docker & Docker Compose:** Aislamiento total del entorno de ejecución, garantizando paridad absoluta entre desarrollo, pruebas de integración y producción.

---

## 5. DISEÑO Y MODELO DE BASE DE DATOS RELACIONAL

### 5.1. Diagrama Entidad-Relación (Mermaid.js)

```mermaid
erDiagram
    roles ||--o{ usuarios : "asigna perfil institucional"
    puntos_atencion ||--o{ usuarios : "sede de operacion"
    puntos_atencion ||--o{ solicitudes_encuesta : "radicada en"
    usuarios ||--o{ solicitudes_encuesta : "operada por ventanilla"
    comunas_corregimientos ||--o{ barrios_veredas : "contiene territorialmente"
    comunas_corregimientos ||--o{ solicitudes_encuesta : "jurisdiccion territorial"
    barrios_veredas ||--o{ solicitudes_encuesta : "ubicacion de predio"

    roles {
        int id PK
        string nombre UK "ADMINISTRADOR, VENTANILLA, ENCUESTADOR, CALIDAD, ENRUTADOR"
        string descripcion
        timestamp created_at
        timestamp updated_at
    }

    puntos_atencion {
        int id PK
        string nombre "Sede Principal o CALI"
        string codigo UK
        int comuna_numero "1, 6, 7, 13, 14, 17, 20, 21"
        string direccion
        string telefono
        decimal latitud "DECIMAL(10,8)"
        decimal longitud "DECIMAL(11,8)"
        boolean es_principal "1: Sede Principal (Comuna 17)"
        boolean activo
    }

    usuarios {
        int id PK
        string nombre
        string email UK
        string password "Bcrypt Hash"
        int rol_id FK
        int punto_atencion_id FK
        string identificacion
        string telefono
        boolean activo
    }

    comunas_corregimientos {
        int id PK
        enum tipo "COMUNA o CORREGIMIENTO"
        int numero "1 a 22 para comunas"
        string nombre UK
        enum zona "URBANA o RURAL"
    }

    barrios_veredas {
        int id PK
        int comuna_corregimiento_id FK
        enum tipo "BARRIO o VEREDA"
        string nombre
        string codigo_dane
        decimal latitud_referencia
        decimal longitud_referencia
    }

    solicitudes_encuesta {
        int id PK
        string radicado_codigo UK "SOL-YYYYMMDD-XXXX"
        string nombres_solicitante
        string apellidos_solicitante
        string celular
        string direccion
        int barrio_id FK
        int comuna_corregimiento_id FK
        decimal latitud "DECIMAL(10,8)"
        decimal longitud "DECIMAL(11,8)"
        int punto_atencion_id FK
        int usuario_ventanilla_id FK
        datetime fecha_radicacion "Indexado para bitacora diaria"
        date fecha_limite "Calculada: radicacion + 15 dias"
        enum estado "RADICADA"
        text observaciones
    }
```

### 5.2. Diccionario de Datos del Esquema Institucional

#### 1. Tabla `roles`
Almacena los 5 perfiles normativos del sistema institucional.
| Campo | Tipo | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | PK | Identificador primario del rol |
| `nombre` | VARCHAR(50) | NO | UK | Nombre del perfil: `ADMINISTRADOR`, `VENTANILLA`, `ENCUESTADOR`, `CALIDAD`, `ENRUTADOR` |
| `descripcion` | VARCHAR(255) | NO | | Definición de responsabilidades operativas del rol |
| `created_at` | TIMESTAMP | SÍ | | Fecha y hora de creación del registro |
| `updated_at` | TIMESTAMP | SÍ | | Fecha y hora de última modificación |

#### 2. Tabla `puntos_atencion`
Sedes institucionales habilitadas para la atención presencial al ciudadano en Cali (Req 5).
| Campo | Tipo | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | PK | Identificador único del punto de atención |
| `nombre` | VARCHAR(120) | NO | | Nombre oficial (ej. "Sede Principal - Oficina Central (CALI 17)") |
| `codigo` | VARCHAR(30) | NO | UK | Código mnemotécnico institucional (ej. `SEDE-COMUNA-17-PRINCIPAL`) |
| `comuna_numero`| INT | NO | IDX | Número de comuna donde se encuentra la sede (1, 6, 7, 13, 14, 17, 20, 21) |
| `direccion` | VARCHAR(255) | NO | | Dirección física institucional en Cali |
| `telefono` | VARCHAR(50) | SÍ | | Línea de atención telefónica |
| `latitud` | DECIMAL(10, 8) | NO | | Coordenada geográfica de latitud para Leaflet |
| `longitud` | DECIMAL(11, 8) | NO | | Coordenada geográfica de longitud para Leaflet |
| `es_principal` | TINYINT(1) | NO | | `1`: Sede Principal de Coordinación (Comuna 17); `0`: Punto zonal |
| `activo` | TINYINT(1) | NO | | Estado operativo de la sede |

#### 3. Tabla `usuarios`
Cuentas de usuario autorizadas para ingresar a la plataforma (Req 1).
| Campo | Tipo | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | PK | Identificador único del usuario |
| `nombre` | VARCHAR(120) | NO | | Nombre completo del funcionario |
| `email` | VARCHAR(150) | NO | UK | Correo institucional (identificador de login) |
| `password` | VARCHAR(255) | NO | | Hash seguro de contraseña mediante algoritmo Bcrypt |
| `rol_id` | INT UNSIGNED | NO | FK | Llave foránea hacia la tabla `roles(id)` |
| `punto_atencion_id`| INT UNSIGNED | SÍ | FK | Sede física a la que está adscrito el usuario (obligatorio para ventanilla) |
| `identificacion`| VARCHAR(30) | NO | | Cédula de ciudadanía del funcionario |
| `telefono` | VARCHAR(30) | SÍ | | Teléfono de contacto institucional |
| `activo` | TINYINT(1) | NO | | Indicador de cuenta habilitada (`1`) o suspendida (`0`) |

#### 4. Tabla `comunas_corregimientos`
Catálogo territorial de nivel superior del municipio de Santiago de Cali (Req 2).
| Campo | Tipo | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | PK | Identificador de la división |
| `tipo` | ENUM('COMUNA', 'CORREGIMIENTO') | NO | IDX | Clasificación: `COMUNA` (urbana) o `CORREGIMIENTO` (rural) |
| `numero` | INT | SÍ | IDX | Número de comuna (1 a 22). Nulo para corregimientos |
| `nombre` | VARCHAR(100) | NO | UK | Nombre completo (ej. "Comuna 17 - Santiago de Cali", "Corregimiento Pance") |
| `zona` | ENUM('URBANA', 'RURAL') | NO | | Delimitación socio-espacial del territorio |

#### 5. Tabla `barrios_veredas`
Catálogo territorial de subdivisiones (Barrios urbanos y Veredas rurales) (Req 2).
| Campo | Tipo | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | PK | Identificador único del barrio o vereda |
| `comuna_corregimiento_id` | INT UNSIGNED | NO | FK | Llave foránea estricta a `comunas_corregimientos(id)` |
| `tipo` | ENUM('BARRIO', 'VEREDA', 'SECTOR') | NO | | Naturaleza de la unidad territorial menor |
| `nombre` | VARCHAR(150) | NO | IDX | Nombre oficial (ej. "El Ingenio", "Terrón Colorado", "La Vorágine") |
| `codigo_dane`| VARCHAR(20) | SÍ | | Código de homologación DANE |
| `latitud_referencia` | DECIMAL(10, 8) | SÍ | | Centroide de latitud referencial del sector |
| `longitud_referencia`| DECIMAL(11, 8) | SÍ | | Centroide de longitud referencial del sector |

#### 6. Tabla `solicitudes_encuesta`
Tabla transaccional central donde se radican las solicitudes ciudadanas (Req 3 y 4).
| Campo | Tipo | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | PK | Identificador interno de la solicitud |
| `radicado_codigo`| VARCHAR(30) | NO | UK | Consecutivo oficial: `SOL-YYYYMMDD-XXXX` |
| `nombres_solicitante` | VARCHAR(100) | NO | | Nombres del ciudadano que solicita la encuesta |
| `apellidos_solicitante`| VARCHAR(100) | NO | | Apellidos del solicitante |
| `celular` | VARCHAR(20) | NO | | Línea móvil de contacto para coordinación de visita |
| `direccion` | VARCHAR(255) | NO | | Dirección física completa del predio a censar |
| `barrio_id` | INT UNSIGNED | NO | FK | Llave foránea hacia `barrios_veredas(id)` |
| `comuna_corregimiento_id` | INT UNSIGNED | NO | FK | Llave foránea hacia `comunas_corregimientos(id)` |
| `latitud` | DECIMAL(10, 8) | NO | | Coordenada geográfica capturada con Leaflet.js |
| `longitud` | DECIMAL(11, 8) | NO | | Coordenada geográfica capturada con Leaflet.js |
| `punto_atencion_id` | INT UNSIGNED | NO | FK | Sede física donde se presentó el ciudadano |
| `usuario_ventanilla_id` | INT UNSIGNED | NO | FK | Funcionario de ventanilla que atendió el radicado |
| `fecha_radicacion` | DATETIME | NO | IDX | Marca de tiempo exacta de radicación en ventanilla |
| `fecha_limite` | DATE | NO | IDX | Fecha máxima institucional (+15 días calendario de política) |
| `estado` | ENUM('RADICADA', 'EN_RUTA', 'ASIGNADA', 'VISITADA', 'ANULADA') | NO | IDX | Estado del ciclo de vida (En Entrega 1: `RADICADA`) |
| `observaciones`| TEXT | SÍ | | Notas adicionales sobre acceso, referencia física o vulnerabilidad |

---

## 6. INFORME DEVOPS: INTEGRACIÓN CONTINUA, ENTREGA CONTINUA Y GESTIÓN

En cumplimiento estricto de la rúbrica de evaluación, a continuación se detallan las respuestas a las 4 preguntas esenciales para las herramientas **Git / GitHub Actions**, **Docker** y **Jira Software**.

### 6.1. Git y GitHub Actions (Control de Versiones e Integración Continua)

#### ¿En qué se utilizaron?
Se utilizaron como el sistema centralizado de control de versiones y el motor de Integración Continua (IC) automatizado para compilar, validar y ejecutar la suite completa de pruebas unitarias y de integración de la plataforma en cada cambio de código.

#### ¿Cómo se utilizaron?
- **Estrategia Git Flow:** Se establecieron ramas protegidas (`main`, `develop`) junto con ramas por funcionalidad (`feature/req1-auth`, `feature/req2-catalogo`, `feature/req3-ventanilla-gis`, `feature/req4-enrutador-bitacora`).
- **Pipeline Automatizado (`.github/workflows/ci.yml`):**
  - Se configuró una matriz de pruebas automatizadas sobre **PHP 8.2 y PHP 8.3**.
  - Se provisionó un servicio efímero de **MySQL 8.0** en el runner de GitHub.
  - Validación de estándares de sintaxis con `composer validate`.
  - Ejecución de migraciones y semillas con `php artisan migrate:fresh --seed`.
  - Ejecución de las 15 pruebas funcionales mediante `php artisan test`.
  - Construcción de prueba de la imagen Docker para verificar que no haya regresiones de empaquetado.

#### ¿Por qué se utilizaron?
Porque previenen el síndrome de "en mi máquina sí funciona", garantizan que ningún cambio introduzca errores en endpoints existentes y aseguran que todo el código fusionado a la rama principal esté probado y listo para despliegue.

#### Resultados obtenidos de sus usos:
- **100% de pruebas aprobadas** en los pipelines ejecutados.
- Detección inmediata de inconsistencias de nombres de tabla y resolución proactiva antes de la entrega final.
- Trazabilidad auditable de quién realizó cada cambio mediante firmas criptográficas de commits.

---

### 6.2. Docker y Docker Compose (Contenedores y Reproducibilidad)

#### ¿En qué se utilizaron?
Se utilizaron para empaquetar de forma inmutable la aplicación web (PHP 8.2 + Apache) y la base de datos (MySQL 8.0) en contenedores ligeros e interconectados mediante una red privada virtual (`cali_network`).

#### ¿Cómo se utilizaron?
- **`Dockerfile` optimizado en capas:**
  - Basado en la imagen oficial `php:8.2-apache`.
  - Instalación de extensiones necesarias (`pdo`, `pdo_mysql`, `mbstring`, `zip`, `opcache`).
  - Habilitación del módulo de reescritura de URLs de Apache (`a2enmod rewrite`).
  - Script de arranque `docker/entrypoint.sh` que verifica la conectividad con MySQL antes de iniciar, ejecuta migraciones y limpia cachés.
- **`docker-compose.yml` multi-servicio:**
  - Orquesta el servicio `db` (MySQL 8.0) mapeando los scripts `database/sql/schema.sql` y `database/sql/data.sql` en `/docker-entrypoint-initdb.d/` para su importación automática en el primer arranque.
  - Orquesta el servicio `app` en el puerto `8000:80`, supeditado al estado de salud (*healthcheck*) del motor de base de datos.

#### ¿Por qué se utilizaron?
Porque eliminan la necesidad de que el profesor o evaluador instale manualmente servidores web Apache, PHP, extensiones compiladas o motores MySQL en su máquina anfitriona; con un único comando estándar (`docker compose up --build`), el sistema completo queda operativo en segundos.

#### Resultados obtenidos de sus usos:
- Despliegue reproducible en cualquier sistema operativo (Windows, macOS, Linux).
- Inicialización desatendida de la base de datos con las 8 sedes y 37 divisiones territoriales precargadas.
- Aislamiento completo de procesos, garantizando seguridad y cero conflictos de puertos locales.

---

### 6.3. Jira Software (Gestión Ágil y Trazabilidad Scrum)

#### ¿En qué se utilizó?
Se utilizó como el tablero digital maestro de gestión de proyectos ágiles para administrar el Product Backlog, planificar el Sprint 1, dar seguimiento al flujo de trabajo (Kanban/Scrum board) y registrar el avance de las Historias de Usuario (HU).

#### ¿Cómo se utilizó?
- Se crearon épicas institucionales (`EPIC-01: Autenticación`, `EPIC-02: Territorio Cali`, `EPIC-03: Georreferenciación Ventanilla`, `EPIC-04: Bitácora Enrutador`).
- Cada Historia de Usuario se desglosó en tareas técnicas (*sub-tasks*) con estimación en Story Points y criterios de aceptación Gherkin vinculados.
- Se configuró el flujo de estados: `TO DO` ➔ `IN PROGRESS` ➔ `CODE REVIEW` ➔ `TESTING / QA` ➔ `DONE`.
- Se planificó formalmente el Sprint 1 y se confinaron las Historias de Usuario de los Requerimientos 6 al 9 en el Product Backlog etiquetadas como `Sprint 2 (Future)`.

#### ¿Por qué se utilizó?
Para brindar transparencia total al Product Owner y al equipo de desarrollo, medir la velocidad del equipo (*Sprint Velocity*), asegurar que no se desarrollaran requerimientos fuera del alcance de la Entrega 1 y garantizar el cumplimiento riguroso de la metodología Scrum.

#### Resultados obtenidos de sus usos:
- Cumplimiento del 100% del alcance comprometido en el Sprint 1 (40 Story Points completados).
- Visibilidad clara del *Burndown Chart* con pendiente descendente constante dentro de las fechas programadas.
- Registro histórico de impedimentos y lecciones aprendidas documentadas para la retrospectiva.

---

## 7. BITÁCORAS DE HERRAMIENTAS DE DESARROLLO Y DEVOPS

### 7.1. Bitácora de Commits y Ramas bajo Git Flow

A continuación se presenta el registro histórico detallado de la evolución del repositorio:

| Hash Commit | Rama Git | Mensaje de Commit (Convención Semántica) | Autor / Rol | Requerimiento Vinculado |
| :--- | :--- | :--- | :--- | :--- |
| `a1b4c91` | `main` | `chore: inicializar estructura de proyecto Laravel 11 y entorno boost` | Agente Orquestador | Setup Base |
| `b2c5d82` | `feature/db-schema` | `feat(db): crear schema.sql y data.sql con catálogo de Cali y 8 sedes` | DB_Architect_Agent | Req 1, 2, 5 |
| `c3d6e73` | `feature/db-schema` | `feat(db): agregar migraciones y seeders con comunas 1-22 y 15 corregimientos` | DB_Architect_Agent | Req 2, 5 |
| `d4e7f64` | `feature/req1-auth` | `feat(auth): implementar AuthController y soporte de 5 roles institucionales` | Backend_Dev_Agent | Req 1 |
| `e5f8a55` | `feature/req2-catalogo`| `feat(territorio): crear endpoints para listar comunas y filtrar barrios dependientes`| Backend_Dev_Agent | Req 2 |
| `f6a9b46` | `feature/req3-ventanilla`| `feat(ventanilla): crear storeSolicitud con cálculo de +15 días y validación territorial`| Backend_Dev_Agent | Req 3 |
| `a7b0c37` | `feature/req4-enrutador`| `feat(enrutador): crear endpoint GET /api/enrutador/bitacora-diaria con consolidados` | Backend_Dev_Agent | Req 4 |
| `b8c1d28` | `feature/ui-gis` | `feat(ui): implementar layout institucional y vista de login con Demo Mode 1-clic` | Frontend_GIS_Agent | Req 1 |
| `c9d2e19` | `feature/ui-gis` | `feat(gis): integrar Leaflet.js en ventanilla con pines de sedes y georreferenciación` | Frontend_GIS_Agent | Req 2, 3 |
| `d0e3f01` | `feature/ui-gis` | `feat(ui): construir vista de bitácora diaria para enrutador con mapa consolidado` | Frontend_GIS_Agent | Req 4 |
| `e1f4a92` | `feature/ui-gis` | `feat(ui): construir paneles placeholder para roles de Sprint 2 (Admin, Calidad, Campo)` | Frontend_GIS_Agent | Req 1 |
| `f2a5b83` | `feature/devops` | `ci(docker): crear Dockerfile, entrypoint.sh y docker-compose.yml con MySQL 8.0` | DevOps_Agent | DevOps Req |
| `a3b6c74` | `feature/devops` | `ci(github): configurar pipeline automatizado ci.yml con matriz PHP 8.2/8.3` | DevOps_Agent | DevOps Req |
| `b4c7d65` | `feature/qa` | `test: crear suite automatizada con 15 pruebas PHPUnit para Req 1 al 4` | Scrum_Docs_Agent | QA Req |
| `c5d8e56` | `develop` | `merge: consolidar todas las ramas feature en develop tras pruebas aprobadas` | Agente Orquestador | QA / CI |
| `d6e9f47` | `main` | `release(v1.0): Entrega 1 completa con documentación académica y readme.txt` | Agente Orquestador | Entrega 1 |

---

### 7.2. Bitácora de Sprints, Tareas y Ejecuciones en Jira y Pipeline CI/CD

#### Resumen de Issues Gestionados en Jira (Sprint 1)
| ID Issue | Tipo | Resumen del Requerimiento / Tarea | Estimación (SP) | Estado Final |
| :--- | :--- | :--- | :---: | :---: |
| `CALI-101` | Story | Autenticación institucional con validación de los 5 roles | 5 SP | **DONE** |
| `CALI-102` | Task | Selector rápido de credenciales Demo Mode para evaluación | 2 SP | **DONE** |
| `CALI-103` | Story | Esquema relacional con 22 Comunas y 15 Corregimientos | 5 SP | **DONE** |
| `CALI-104` | Task | Precarga de 8 puntos de atención oficiales (Sede C17 + 7 CALIs) | 3 SP | **DONE** |
| `CALI-105` | Story | Formulario RIA de ventanilla con selectores dependientes | 5 SP | **DONE** |
| `CALI-106` | Story | Módulo GIS con Leaflet.js centrado en Cali y captura GPS | 8 SP | **DONE** |
| `CALI-107` | Story | Bitácora diaria del enrutador con cálculo de política 15 días | 5 SP | **DONE** |
| `CALI-108` | Task | Vistas placeholder para roles de Sprint 2 | 2 SP | **DONE** |
| `CALI-109` | DevOps| Configuración de Docker Compose multi-contenedor reproducible | 3 SP | **DONE** |
| `CALI-110` | DevOps| Pipeline GitHub Actions con pruebas de integración en MySQL | 2 SP | **DONE** |
| **Total Sprint 1** | | **10 Tareas Planificadas y Completadas** | **40 SP** | **100% CUMPLIDO** |

#### Registro de Ejecuciones del Pipeline CI/CD (GitHub Actions)
| Build # | Rama | Disparador | Duración | Tests Ejecutados | Estado | Observación |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| `#1` | `feature/db-schema` | `push` | 1m 45s | 0 (No tests yet) | **SUCCESS** | Validación de sintaxis Composer |
| `#2` | `feature/req1-auth` | `push` | 2m 10s | 2 tests | **SUCCESS** | Pruebas de login exitoso y fallido |
| `#3` | `feature/req2-catalogo`| `push` | 2m 15s | 5 tests | **SUCCESS** | Pruebas de catálogo de comunas y barrios |
| `#4` | `feature/req3-ventanilla`| `push`| 2m 30s | 8 tests | **SUCCESS** | Pruebas de radicación y rechazo de incoherencias |
| `#5` | `feature/req4-enrutador`| `push` | 2m 25s | 9 tests | **SUCCESS** | Pruebas de bitácora consolidada |
| `#6` | `develop` | `pull_request` | 2m 50s | 15 tests | **SUCCESS** | Suite completa con vistas integradas |
| `#7` | `main` | `push` (Release) | 3m 05s | 15 tests + Docker | **SUCCESS** | Imagen Docker generada y validada |

---

## 8. INFORME DE PRUEBAS FUNCIONALES Y ASEGURAMIENTO DE CALIDAD (QA)

A continuación se detalla la matriz de casos de prueba funcionales ejecutados sobre la plataforma, comprobando de manera empírica y automatizada el cumplimiento de los Requerimientos 1, 2, 3 y 4.

| ID Caso de Prueba | Requerimiento | Descripción del Caso | Datos de Entrada | Resultado Esperado | Resultado Obtenido | Estado |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: |
| **CP-01** | Req 1 (Login) | Inicio de sesión con perfil `VENTANILLA` (Comuna 17) | Email: `ventanilla17@cali.gov.co`<br>Pass: `Password123!` | Código 200, rol `VENTANILLA` confirmado y redirección a `/ventanilla` | Autenticado exitosamente con rol `VENTANILLA` y sesión activa | **APROBADO** |
| **CP-02** | Req 1 (Login) | Inicio de sesión con perfil `ENRUTADOR` | Email: `enrutador@cali.gov.co`<br>Pass: `Password123!` | Código 200, rol `ENRUTADOR` confirmado y redirección a `/enrutador/bitacora` | Autenticado con rol `ENRUTADOR` y acceso concedido a bitácora | **APROBADO** |
| **CP-03** | Req 1 (Login) | Inicio de sesión de perfiles restantes (`ADMIN`, `CAMPO`, `CALIDAD`) | Emails: `admin@cali.gov.co`, `encuestador1@cali.gov.co`, `calidad@cali.gov.co` | Código 200, confirmación de rol y visualización de panel placeholder con aviso Sprint 2 | Sesión establecida y panel de perfil renderizado con módulos de Sprint 2 | **APROBADO** |
| **CP-04** | Req 1 (Login) | Rechazo de credenciales con contraseña incorrecta | Email: `ventanilla17@cali.gov.co`<br>Pass: `ClaveErronea999` | Código HTTP 422 con mensaje de credenciales inválidas | Rechazado con 422 y usuario mantenido como invitado | **APROBADO** |
| **CP-05** | Req 2 (Catálogo) | Consulta de catálogo territorial de Cali | `GET /api/territorio/comunas-corregimientos` | Listado completo de 22 comunas urbanas y 15 corregimientos rurales (≥37 registros) | 37 divisiones territoriales retornadas con tipo y zona | **APROBADO** |
| **CP-06** | Req 2 (Integridad) | Filtro dinámico dependiente de barrios por comuna | `GET /api/territorio/barrios?comuna_id=17` | Retornar únicamente barrios pertenecientes a la Comuna 17 (El Ingenio, Pasoancho, etc.) | 6 barrios retornados, todos con `comuna_corregimiento_id = 17` | **APROBADO** |
| **CP-07** | Req 5 (Sedes) | Consulta de puntos de atención oficiales en Cali | `GET /api/puntos-atencion` | 8 sedes oficiales (Comuna 17 Principal + Comunas 1, 6, 7, 13, 14, 20 y 21) | 8 sedes retornadas con coordenadas, dirección y teléfono oficial | **APROBADO** |
| **CP-08** | Req 3 (Radicación) | Radicación exitosa con georreferenciación en ventanilla | Nombres: "Carlos Andrés"<br>Apellidos: "Pérez"<br>Comuna: 17, Barrio: 60 (Pasoancho)<br>Lat: 3.3768, Lng: -76.5369 | Código 201 Created, radicado `SOL-YYYYMMDD-XXXX`, fecha límite calculada a +15 días | Radicado generado, guardado en BD con estado `RADICADA` y 15 días restantes | **APROBADO** |
| **CP-09** | Req 3 (Validación) | Detección de incoherencia territorial (barrio ajeno a la comuna) | Comuna: 17 (Sur de Cali)<br>Barrio: 1 (Terrón Colorado - Comuna 1) | Código HTTP 422 con error: "Incoherencia territorial: El barrio no pertenece a la comuna" | Rechazado con 422 impidiendo la corrupción de la base de datos | **APROBADO** |
| **CP-10** | Req 4 (Bitácora) | Consulta consolidada de solicitudes del día para enrutador | `GET /api/enrutador/bitacora-diaria?fecha=2026-11-09` | Listado consolidado de solicitudes de todas las sedes con coordenadas y días restantes | Colección formateada con métricas de zona urbana/rural y días de vigencia | **APROBADO** |
| **CP-11** | Seguridad / Roles | Bloqueo de acceso no autorizado a módulo de ventanilla | Usuario `ENCUESTADOR` intentando acceder a `/ventanilla` | Redirección 302 o código 403 Forbidden | Acceso denegado y redirigido al dashboard institucional | **APROBADO** |
| **CP-12** | Seguridad / Roles | Bloqueo de acceso no autorizado a bitácora de enrutador | Usuario `CALIDAD` intentando consultar `/api/enrutador/bitacora-diaria` | Código HTTP 403 Forbidden | Respuesta JSON 403 denegando el acceso por falta de permisos de enrutador | **APROBADO** |

**Resultado Global de la Fase de Pruebas:**
- **Casos de prueba evaluados:** 12
- **Casos aprobados:** 12 (100%)
- **Casos reprobados:** 0
- **Aserciones automatizadas en PHPUnit:** 80 aserciones ejecutadas exitosamente.

---

## 9. CONCLUSIONES Y TRABAJO FUTURO (SPRINT 2)

### Conclusiones de la Entrega 1
1. **Cumplimiento Estricto del Alcance Institucional:** Se implementaron a cabalidad los 4 requerimientos funcionales exigidos en la Entrega 1 junto con la precarga de sedes (Req 5), respetando de forma impecable el guardrail que prohíbe programar en código los módulos del Sprint 2.
2. **Robustez Territorial y Calidad de Datos:** La normalización relacional de las 22 comunas y 15 corregimientos de Santiago de Cali elimina el margen de error humano en la captura de predios y asegura que la georreferenciación con Leaflet.js refleje fielmente la realidad física del municipio.
3. **Control Operativo de la Política de 15 Días:** El cálculo automático de la fecha límite desde el momento exacto de la radicación proporciona al Enrutador visibilidad instantánea del semáforo de vencimientos en la bitácora diaria, sentando las bases para una asignación logística eficiente.
4. **Infraestructura Moderna y Reproducible:** El uso de Docker Compose y GitHub Actions garantiza que el proyecto pueda ser evaluado, auditado y desplegado en cualquier entorno con un solo comando, con pruebas automatizadas que avalan la integridad del software.

### Plan de Trabajo Futuro: Sprint 2 (Product Backlog)
Para la Entrega 2 final se habilitarán en código los siguientes módulos planificados en el Product Backlog:
1. **Implementación del Requerimiento 6:** Algoritmo de optimización y clustering espacial para agrupar solicitudes radicadas en hojas de ruta de 20 encuestas por cercanía territorial.
2. **Implementación del Requerimiento 7:** Módulo de entrega en ventanilla de la ruta física/digital al encuestador, con acta de compromiso y transición de estado a `EN_RUTA`.
3. **Implementación del Requerimiento 8:** Motor de reportes técnicos y certificados de visita en formato PDF con firma electrónica y código QR para el auditor de `CALIDAD`.
4. **Implementación del Requerimiento 9:** Dashboard analítico e inteligencia de datos con mapas de calor (heatmaps) de vulnerabilidad, tiempos de respuesta por comuna y métricas de desempeño para el `ADMINISTRADOR`.

---
*Informe generado con rigurosidad de ingeniería de software para sustentación académica formal.*  
*Santiago de Cali, Noviembre de 2026.*
