-- =====================================================================
-- SISTEMA DE GESTIÓN Y GEORREFERENCIACIÓN DE ENCUESTAS - SANTIAGO DE CALI
-- ENTREGA 1 - DATOS INICIALES Y CATÁLOGOS TERRITORIALES (MySQL 8.0)
-- Sub-Agente 1: DB_Architect_Agent
-- =====================================================================

USE gestion_encuestas_cali;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. PRECARGA DE ROLES INSTITUCIONALES (5 Perfiles Exigidos)
-- ---------------------------------------------------------------------
TRUNCATE TABLE roles;
INSERT INTO roles (id, nombre, descripcion, created_at, updated_at) VALUES
(1, 'ADMINISTRADOR', 'Administrador general del sistema institucional y control maestro', NOW(), NOW()),
(2, 'VENTANILLA', 'Operador de atención al ciudadano y radicación georreferenciada en sede', NOW(), NOW()),
(3, 'ENCUESTADOR', 'Personal operativo de campo para levantamiento de encuestas domiciliarias', NOW(), NOW()),
(4, 'CALIDAD', 'Auditor de calidad y verificación metodológica de encuestas', NOW(), NOW()),
(5, 'ENRUTADOR', 'Coordinador logístico de bitácora diaria y consolidación territorial', NOW(), NOW());

-- ---------------------------------------------------------------------
-- 2. PRECARGA DE PUNTOS DE ATENCIÓN OFICIALES EN CALI
-- Sedes: Comunas 1, 6, 7, 13, 14, 20, 21 y Sede Principal (Comuna 17)
-- ---------------------------------------------------------------------
TRUNCATE TABLE puntos_atencion;
INSERT INTO puntos_atencion (id, nombre, codigo, comuna_numero, direccion, telefono, latitud, longitud, es_principal, activo, created_at, updated_at) VALUES
(1, 'Sede Principal - Oficina Central (CALI 17)', 'SEDE-COMUNA-17-PRINCIPAL', 17, 'Calle 13B # 85-10, Barrio Pasoancho', '(602) 8879020', 3.37652400, -76.53671200, 1, 1, NOW(), NOW()),
(2, 'Punto de Atención CALI 1 - Terrón Colorado', 'SEDE-COMUNA-01', 1, 'Avenida 4A Oeste # 19-00, Terrón Colorado', '(602) 8933333', 3.45321800, -76.55621400, 0, 1, NOW(), NOW()),
(3, 'Punto de Atención CALI 6 - Los Guaduales', 'SEDE-COMUNA-06', 6, 'Carrera 8N # 70A-16, Los Guaduales', '(602) 4481234', 3.48624100, -76.49503200, 0, 1, NOW(), NOW()),
(4, 'Punto de Atención CALI 7 - Alfonso López', 'SEDE-COMUNA-07', 7, 'Calle 73 # 7G-34, Alfonso López I', '(602) 4415566', 3.46581200, -76.48804500, 0, 1, NOW(), NOW()),
(5, 'Punto de Atención CALI 13 - El Poblado', 'SEDE-COMUNA-13', 13, 'Calle 72P con Carrera 28D, El Poblado II', '(602) 4208899', 3.41872300, -76.49651800, 0, 1, NOW(), NOW()),
(6, 'Punto de Atención CALI 14 - Marroquín', 'SEDE-COMUNA-14', 14, 'Carrera 27 # 78A-04, Marroquín II', '(602) 4492211', 3.42514500, -76.48202900, 0, 1, NOW(), NOW()),
(7, 'Punto de Atención CALI 20 - Siloé', 'SEDE-COMUNA-20', 20, 'Carrera 52 con Calle 2, Siloé', '(602) 5521098', 3.42051200, -76.55403100, 0, 1, NOW(), NOW()),
(8, 'Punto de Atención CALI 21 - Desepaz', 'SEDE-COMUNA-21', 21, 'Carrera 23 con Calle 120, Desepaz', '(602) 4307766', 3.43201400, -76.47102500, 0, 1, NOW(), NOW());

-- ---------------------------------------------------------------------
-- 3. PRECARGA DE USUARIOS DE PRUEBA (Credenciales Claras y Verificadas)
-- Contraseña universal de prueba: Password123!
-- Hash Bcrypt: $2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO
-- ---------------------------------------------------------------------
TRUNCATE TABLE usuarios;
INSERT INTO usuarios (id, nombre, email, password, rol_id, punto_atencion_id, identificacion, telefono, activo, created_at, updated_at) VALUES
(1, 'Ing. Carlos Mendoza (Admin)', 'admin@cali.gov.co', '$2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO', 1, 1, '1144123001', '3001234501', 1, NOW(), NOW()),
(2, 'María Fernanda Gómez (Ventanilla Comuna 17)', 'ventanilla17@cali.gov.co', '$2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO', 2, 1, '1144123002', '3001234502', 1, NOW(), NOW()),
(3, 'Julián Andrés Caicedo (Ventanilla Comuna 1)', 'ventanilla1@cali.gov.co', '$2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO', 2, 2, '1144123003', '3001234503', 1, NOW(), NOW()),
(4, 'Pedro Antonio Ramos (Encuestador)', 'encuestador1@cali.gov.co', '$2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO', 3, 1, '1144123004', '3001234504', 1, NOW(), NOW()),
(5, 'Dra. Patricia Salazar (Auditora Calidad)', 'calidad@cali.gov.co', '$2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO', 4, 1, '1144123005', '3001234505', 1, NOW(), NOW()),
(6, 'Ing. Roberto Quintero (Enrutador Logístico)', 'enrutador@cali.gov.co', '$2y$12$XqkYfUs5ddH7ka5rD9tDF.nm/mzKbZTsbCGcMsqUudzY4Whez5KQO', 5, 1, '1144123006', '3001234506', 1, NOW(), NOW());

-- ---------------------------------------------------------------------
-- 4. CATÁLOGO TERRITORIAL: comunas_corregimientos
-- 22 Comunas Urbanas y 15 Corregimientos Rurales de Santiago de Cali
-- ---------------------------------------------------------------------
TRUNCATE TABLE comunas_corregimientos;
INSERT INTO comunas_corregimientos (id, tipo, numero, nombre, zona, created_at) VALUES
(1,  'COMUNA', 1,  'Comuna 1 - San Antonio / Terrón Colorado', 'URBANA', NOW()),
(2,  'COMUNA', 2,  'Comuna 2 - Granada / Santa Mónica', 'URBANA', NOW()),
(3,  'COMUNA', 3,  'Comuna 3 - Centro / San Cayetano', 'URBANA', NOW()),
(4,  'COMUNA', 4,  'Comuna 4 - Santander / Salomia', 'URBANA', NOW()),
(5,  'COMUNA', 5,  'Comuna 5 - Torres de Comfandi / Los Andes', 'URBANA', NOW()),
(6,  'COMUNA', 6,  'Comuna 6 - Los Guaduales / Floralia', 'URBANA', NOW()),
(7,  'COMUNA', 7,  'Comuna 7 - Alfonso López / Las Ceibas', 'URBANA', NOW()),
(8,  'COMUNA', 8,  'Comuna 8 - La Floresta / Las Américas', 'URBANA', NOW()),
(9,  'COMUNA', 9,  'Comuna 9 - Bretaña / Aranjuez / Alameda', 'URBANA', NOW()),
(10, 'COMUNA', 10, 'Comuna 10 - Departamental / San Judas', 'URBANA', NOW()),
(11, 'COMUNA', 11, 'Comuna 11 - 20 de Julio / Aguablanca', 'URBANA', NOW()),
(12, 'COMUNA', 12, 'Comuna 12 - Nueva Floresta / Asturias', 'URBANA', NOW()),
(13, 'COMUNA', 13, 'Comuna 13 - El Poblado / El Vergel', 'URBANA', NOW()),
(14, 'COMUNA', 14, 'Comuna 14 - Marroquín / Los Naranjos', 'URBANA', NOW()),
(15, 'COMUNA', 15, 'Comuna 15 - Morichal / Llano Verde', 'URBANA', NOW()),
(16, 'COMUNA', 16, 'Comuna 16 - Mariano Ramos / República Israel', 'URBANA', NOW()),
(17, 'COMUNA', 17, 'Comuna 17 - El Ingenio / Valle del Lili / Pasoancho', 'URBANA', NOW()),
(18, 'COMUNA', 18, 'Comuna 18 - Meléndez / Polvorines', 'URBANA', NOW()),
(19, 'COMUNA', 19, 'Comuna 19 - San Fernando / Tequendama', 'URBANA', NOW()),
(20, 'COMUNA', 20, 'Comuna 20 - Siloé / La Sultana', 'URBANA', NOW()),
(21, 'COMUNA', 21, 'Comuna 21 - Desepaz / Potrero Grande', 'URBANA', NOW()),
(22, 'COMUNA', 22, 'Comuna 22 - Ciudad Jardín / Pance Urbano', 'URBANA', NOW()),
-- Zona Rural: 15 Corregimientos Oficiales
(23, 'CORREGIMIENTO', NULL, 'Corregimiento Pance', 'RURAL', NOW()),
(24, 'CORREGIMIENTO', NULL, 'Corregimiento La Buitrera', 'RURAL', NOW()),
(25, 'CORREGIMIENTO', NULL, 'Corregimiento Montebello', 'RURAL', NOW()),
(26, 'CORREGIMIENTO', NULL, 'Corregimiento Golondrinas', 'RURAL', NOW()),
(27, 'CORREGIMIENTO', NULL, 'Corregimiento Navarro', 'RURAL', NOW()),
(28, 'CORREGIMIENTO', NULL, 'Corregimiento El Hormiguero', 'RURAL', NOW()),
(29, 'CORREGIMIENTO', NULL, 'Corregimiento Felidia', 'RURAL', NOW()),
(30, 'CORREGIMIENTO', NULL, 'Corregimiento Pichindé', 'RURAL', NOW()),
(31, 'CORREGIMIENTO', NULL, 'Corregimiento Los Andes', 'RURAL', NOW()),
(32, 'CORREGIMIENTO', NULL, 'Corregimiento Villacarmelo', 'RURAL', NOW()),
(33, 'CORREGIMIENTO', NULL, 'Corregimiento La Leonera', 'RURAL', NOW()),
(34, 'CORREGIMIENTO', NULL, 'Corregimiento La Elvira', 'RURAL', NOW()),
(35, 'CORREGIMIENTO', NULL, 'Corregimiento Saladito', 'RURAL', NOW()),
(36, 'CORREGIMIENTO', NULL, 'Corregimiento La Paz', 'RURAL', NOW()),
(37, 'CORREGIMIENTO', NULL, 'Corregimiento La Castilla', 'RURAL', NOW());

-- ---------------------------------------------------------------------
-- 5. CATÁLOGO TERRITORIAL: barrios_veredas
-- Relación estricta y normalizada que previene asignaciones erróneas
-- ---------------------------------------------------------------------
TRUNCATE TABLE barrios_veredas;
INSERT INTO barrios_veredas (id, comuna_corregimiento_id, tipo, nombre, codigo_dane, latitud_referencia, longitud_referencia, created_at) VALUES
-- Comuna 1
(1, 1, 'BARRIO', 'Terrón Colorado', '760010101', 3.4532, -76.5562, NOW()),
(2, 1, 'BARRIO', 'Vista Hermosa', '760010102', 3.4560, -76.5610, NOW()),
(3, 1, 'BARRIO', 'Patio Bonito', '760010103', 3.4510, -76.5520, NOW()),
(4, 1, 'BARRIO', 'Bajo Aguacatal', '760010104', 3.4590, -76.5490, NOW()),
-- Comuna 2
(5, 2, 'BARRIO', 'Granada', '760010201', 3.4580, -76.5330, NOW()),
(6, 2, 'BARRIO', 'Versalles', '760010202', 3.4610, -76.5290, NOW()),
(7, 2, 'BARRIO', 'Santa Mónica', '760010203', 3.4680, -76.5260, NOW()),
(8, 2, 'BARRIO', 'Chipichape', '760010204', 3.4750, -76.5280, NOW()),
(9, 2, 'BARRIO', 'La Flora', '760010205', 3.4830, -76.5240, NOW()),
-- Comuna 3
(10, 3, 'BARRIO', 'San Antonio', '760010301', 3.4470, -76.5410, NOW()),
(11, 3, 'BARRIO', 'El Peñón', '760010302', 3.4505, -76.5390, NOW()),
(12, 3, 'BARRIO', 'San Cayetano', '760010303', 3.4440, -76.5360, NOW()),
(13, 3, 'BARRIO', 'La Merced', '760010304', 3.4530, -76.5340, NOW()),
-- Comuna 4
(14, 4, 'BARRIO', 'Santander', '760010401', 3.4620, -76.5150, NOW()),
(15, 4, 'BARRIO', 'Salomia', '760010402', 3.4690, -76.5080, NOW()),
(16, 4, 'BARRIO', 'Las Delicias', '760010403', 3.4600, -76.5190, NOW()),
-- Comuna 5
(17, 5, 'BARRIO', 'Torres de Comfandi', '760010501', 3.4790, -76.5050, NOW()),
(18, 5, 'BARRIO', 'Los Andes', '760010502', 3.4740, -76.5120, NOW()),
(19, 5, 'BARRIO', 'Paseo de Los Álamos', '760010503', 3.4850, -76.5150, NOW()),
-- Comuna 6
(20, 6, 'BARRIO', 'Los Guaduales', '760010601', 3.4862, -76.4950, NOW()),
(21, 6, 'BARRIO', 'Floralia', '760010602', 3.4910, -76.4910, NOW()),
(22, 6, 'BARRIO', 'Los Alcázares', '760010603', 3.4790, -76.4980, NOW()),
(23, 6, 'BARRIO', 'Paso del Comercio', '760010604', 3.4950, -76.4860, NOW()),
(24, 6, 'BARRIO', 'Petecuy I', '760010605', 3.4820, -76.4870, NOW()),
-- Comuna 7
(25, 7, 'BARRIO', 'Alfonso López I', '760010701', 3.4658, -76.4880, NOW()),
(26, 7, 'BARRIO', 'Puerto Mallarino', '760010702', 3.4610, -76.4780, NOW()),
(27, 7, 'BARRIO', '7 de Agosto', '760010703', 3.4580, -76.4950, NOW()),
(28, 7, 'BARRIO', 'Las Ceibas', '760010704', 3.4680, -76.4930, NOW()),
-- Comuna 8
(29, 8, 'BARRIO', 'La Floresta', '760010801', 3.4490, -76.5080, NOW()),
(30, 8, 'BARRIO', 'Las Américas', '760010802', 3.4440, -76.5040, NOW()),
(31, 8, 'BARRIO', 'Atanasio Girardot', '760010803', 3.4530, -76.5110, NOW()),
-- Comuna 9
(32, 9, 'BARRIO', 'Bretaña', '760010901', 3.4380, -76.5310, NOW()),
(33, 9, 'BARRIO', 'Alameda', '760010902', 3.4420, -76.5340, NOW()),
(34, 9, 'BARRIO', 'Junín', '760010903', 3.4340, -76.5270, NOW()),
-- Comuna 10
(35, 10, 'BARRIO', 'Departamental', '760011001', 3.4210, -76.5280, NOW()),
(36, 10, 'BARRIO', 'San Judas Tadeo', '760011002', 3.4150, -76.5220, NOW()),
(37, 10, 'BARRIO', 'Cristóbal Colón', '760011003', 3.4250, -76.5240, NOW()),
-- Comuna 11
(38, 11, 'BARRIO', '20 de Julio', '760011101', 3.4300, -76.5130, NOW()),
(39, 11, 'BARRIO', 'Prados de Oriente', '760011102', 3.4350, -76.5170, NOW()),
(40, 11, 'BARRIO', 'San Carlos', '760011103', 3.4260, -76.5100, NOW()),
-- Comuna 12
(41, 12, 'BARRIO', 'Nueva Floresta', '760011201', 3.4410, -76.4980, NOW()),
(42, 12, 'BARRIO', 'Asturias', '760011202', 3.4460, -76.4950, NOW()),
(43, 12, 'BARRIO', 'Villanueva', '760011203', 3.4370, -76.4920, NOW()),
-- Comuna 13
(44, 13, 'BARRIO', 'El Poblado II', '760011301', 3.4187, -76.4965, NOW()),
(45, 13, 'BARRIO', 'El Vergel', '760011302', 3.4230, -76.5010, NOW()),
(46, 13, 'BARRIO', 'Los Robles', '760011303', 3.4160, -76.4910, NOW()),
(47, 13, 'BARRIO', 'Calipso', '760011304', 3.4110, -76.5050, NOW()),
-- Comuna 14
(48, 14, 'BARRIO', 'Marroquín II', '760011401', 3.4251, -76.4820, NOW()),
(49, 14, 'BARRIO', 'Los Naranjos', '760011402', 3.4290, -76.4800, NOW()),
(50, 14, 'BARRIO', 'Manuela Beltrán', '760011403', 3.4340, -76.4780, NOW()),
(51, 14, 'BARRIO', 'Las Orquídeas', '760011404', 3.4210, -76.4850, NOW()),
-- Comuna 15
(52, 15, 'BARRIO', 'Morichal de Comfandi', '760011501', 3.4070, -76.5120, NOW()),
(53, 15, 'BARRIO', 'Llano Verde', '760011502', 3.4010, -76.5080, NOW()),
(54, 15, 'BARRIO', 'Ciudad Córdoba', '760011503', 3.4130, -76.5160, NOW()),
-- Comuna 16
(55, 16, 'BARRIO', 'Mariano Ramos', '760011601', 3.4040, -76.5200, NOW()),
(56, 16, 'BARRIO', 'República de Israel', '760011602', 3.4090, -76.5240, NOW()),
(57, 16, 'BARRIO', 'Antonio Nariño', '760011603', 3.4140, -76.5210, NOW()),
-- Comuna 17
(58, 17, 'BARRIO', 'El Ingenio', '760011701', 3.3790, -76.5340, NOW()),
(59, 17, 'BARRIO', 'Valle del Lili', '760011702', 3.3680, -76.5280, NOW()),
(60, 17, 'BARRIO', 'Pasoancho', '760011703', 3.3765, -76.5367, NOW()),
(61, 17, 'BARRIO', 'Limonar', '760011704', 3.3880, -76.5390, NOW()),
(62, 17, 'BARRIO', 'Primero de Mayo', '760011705', 3.3950, -76.5360, NOW()),
(63, 17, 'BARRIO', 'Santa Anita', '760011706', 3.3920, -76.5410, NOW()),
-- Comuna 18
(64, 18, 'BARRIO', 'Meléndez', '760011801', 3.3740, -76.5510, NOW()),
(65, 18, 'BARRIO', 'Polvorines', '760011802', 3.3690, -76.5620, NOW()),
(66, 18, 'BARRIO', 'Los Chorros', '760011803', 3.3820, -76.5580, NOW()),
-- Comuna 19
(67, 19, 'BARRIO', 'San Fernando', '760011901', 3.4280, -76.5440, NOW()),
(68, 19, 'BARRIO', 'Tequendama', '760011902', 3.4170, -76.5430, NOW()),
(69, 19, 'BARRIO', 'Miraflores', '760011903', 3.4350, -76.5460, NOW()),
(70, 19, 'BARRIO', 'El Cedro', '760011904', 3.4310, -76.5380, NOW()),
-- Comuna 20
(71, 20, 'BARRIO', 'Siloé', '760012001', 3.4205, -76.5540, NOW()),
(72, 20, 'BARRIO', 'Tierra Blanca', '760012002', 3.4160, -76.5580, NOW()),
(73, 20, 'BARRIO', 'Belisario Caicedo', '760012003', 3.4230, -76.5510, NOW()),
(74, 20, 'BARRIO', 'La Sultana', '760012004', 3.4260, -76.5590, NOW()),
-- Comuna 21
(75, 21, 'BARRIO', 'Desepaz', '760012101', 3.4320, -76.4710, NOW()),
(76, 21, 'BARRIO', 'Potrero Grande', '760012102', 3.4270, -76.4680, NOW()),
(77, 21, 'BARRIO', 'Compartir', '760012103', 3.4360, -76.4740, NOW()),
(78, 21, 'BARRIO', 'Ciudad Talanga', '760012104', 3.4390, -76.4690, NOW()),
-- Comuna 22
(79, 22, 'BARRIO', 'Ciudad Jardín', '760012201', 3.3610, -76.5350, NOW()),
(80, 22, 'BARRIO', 'Parcelaciones Pance', '760012202', 3.3450, -76.5380, NOW()),
(81, 22, 'BARRIO', 'Campestre', '760012203', 3.3680, -76.5420, NOW()),
-- Zona Rural: Corregimientos y sus Veredas
-- Pance
(82, 23, 'VEREDA', 'Vereda Pance Cabecera', '76001R0101', 3.3210, -76.5640, NOW()),
(83, 23, 'VEREDA', 'La Vorágine', '76001R0102', 3.3340, -76.5520, NOW()),
(84, 23, 'VEREDA', 'San Francisco', '76001R0103', 3.3150, -76.5780, NOW()),
(85, 23, 'VEREDA', 'El Peón', '76001R0104', 3.3020, -76.5890, NOW()),
-- La Buitrera
(86, 24, 'VEREDA', 'Vereda La Buitrera Cabecera', '76001R0201', 3.3640, -76.5720, NOW()),
(87, 24, 'VEREDA', 'Altos de La Buitrera', '76001R0202', 3.3580, -76.5810, NOW()),
(88, 24, 'VEREDA', 'El Rosario', '76001R0203', 3.3710, -76.5690, NOW()),
-- Montebello
(89, 25, 'VEREDA', 'Vereda Montebello Centro', '76001R0301', 3.4780, -76.5620, NOW()),
(90, 25, 'VEREDA', 'Campo Alegre', '76001R0302', 3.4830, -76.5690, NOW()),
-- Golondrinas
(91, 26, 'VEREDA', 'Vereda Golondrinas Cabecera', '76001R0401', 3.5010, -76.5420, NOW()),
(92, 26, 'VEREDA', 'La María Rural', '76001R0402', 3.5090, -76.5380, NOW()),
-- Navarro
(93, 27, 'VEREDA', 'Vereda Navarro Cabecera', '76001R0501', 3.3850, -76.4820, NOW()),
(94, 27, 'VEREDA', 'Sector El Pondaje Rural', '76001R0502', 3.3920, -76.4750, NOW()),
-- El Hormiguero
(95, 28, 'VEREDA', 'Vereda Morgan', '76001R0601', 3.3410, -76.4780, NOW()),
(96, 28, 'VEREDA', 'Cascajal', '76001R0602', 3.3320, -76.4690, NOW()),
-- Felidia
(97, 29, 'VEREDA', 'Vereda Felidia Cabecera', '76001R0701', 3.4730, -76.6210, NOW()),
(98, 29, 'VEREDA', 'Las Nieves', '76001R0702', 3.4810, -76.6340, NOW()),
-- Pichindé
(99, 30, 'VEREDA', 'Vereda Pichindé Cabecera', '76001R0801', 3.4410, -76.6180, NOW()),
(100, 30, 'VEREDA', 'Peñas Blancas', '76001R0802', 3.4320, -76.6350, NOW()),
-- Los Andes
(101, 31, 'VEREDA', 'Vereda Quebrada Honda', '76001R0901', 3.4210, -76.5890, NOW()),
(102, 31, 'VEREDA', 'Pueblo Nuevo', '76001R0902', 3.4150, -76.5980, NOW()),
-- Villacarmelo
(103, 32, 'VEREDA', 'La Gallera', '76001R1001', 3.3780, -76.6020, NOW()),
(104, 32, 'VEREDA', 'El Carmen Rural', '76001R1002', 3.3710, -76.6120, NOW()),
-- La Leonera
(105, 33, 'VEREDA', 'Vereda La Leonera Cabecera', '76001R1101', 3.4560, -76.6390, NOW()),
-- La Elvira
(106, 34, 'VEREDA', 'Vereda La Elvira Km 18', '76001R1201', 3.5240, -76.5890, NOW()),
-- Saladito
(107, 35, 'VEREDA', 'Vereda El Saladito Cabecera', '76001R1301', 3.4890, -76.5920, NOW()),
-- La Paz
(108, 36, 'VEREDA', 'Vereda La Paz Rural', '76001R1401', 3.5120, -76.6110, NOW()),
-- La Castilla
(109, 37, 'VEREDA', 'Vereda La Castilla Centro', '76001R1501', 3.4980, -76.5740, NOW());

-- ---------------------------------------------------------------------
-- 6. SOLICITUDES DE PRUEBA PRECARGADAS (Consolidación para Bitácora Req 4)
-- Radicadas hoy y días recientes en diferentes sedes (urbanas y rurales)
-- Fecha límite = fecha_radicación + 15 días calendario
-- ---------------------------------------------------------------------
TRUNCATE TABLE solicitudes_encuesta;
INSERT INTO solicitudes_encuesta 
(id, radicado_codigo, nombres_solicitante, apellidos_solicitante, celular, direccion, barrio_id, comuna_corregimiento_id, latitud, longitud, punto_atencion_id, usuario_ventanilla_id, fecha_radicacion, fecha_limite, estado, observaciones, created_at, updated_at) 
VALUES
(1, 'SOL-20261109-0001', 'Ana Lucía', 'Torres Benítez', '3157894512', 'Carrera 83C # 14-22, Apto 301', 58, 17, 3.37920000, -76.53410000, 1, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Solicitud para evaluación de estratificación y SISBEN IV en El Ingenio', NOW(), NOW()),
(2, 'SOL-20261109-0002', 'Héctor Fabio', 'Montaño Ruiz', '3104561234', 'Avenida 4A Oeste # 21-15', 1, 1, 3.45380000, -76.55680000, 2, 3, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Vivienda unifamiliar sector Terrón Colorado', NOW(), NOW()),
(3, 'SOL-20261109-0003', 'Gloria Inés', 'Valencia Cardona', '3189901122', 'Calle 70A # 8N-45', 20, 6, 3.48650000, -76.49520000, 3, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Madre cabeza de hogar requiere encuesta urgente', NOW(), NOW()),
(4, 'SOL-20261109-0004', 'Jorge Eliécer', 'Giraldo Soto', '3123456789', 'Carrera 7G # 74-12', 25, 7, 3.46600000, -76.48830000, 4, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Alfonso López I sector puente', NOW(), NOW()),
(5, 'SOL-20261109-0005', 'Martha Cecilia', 'Díaz Ortiz', '3166543210', 'Calle 72Q # 28E-18', 44, 13, 3.41890000, -76.49680000, 5, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Poblado II cerca a polideportivo', NOW(), NOW()),
(6, 'SOL-20261109-0006', 'Jhon Jairo', 'Mina Carabalí', '3171122334', 'Carrera 27A # 79-30', 48, 14, 3.42530000, -76.48220000, 6, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Marroquín II sector oriente', NOW(), NOW()),
(7, 'SOL-20261109-0007', 'Sandra Milena', 'Osorio Vega', '3145566778', 'Carrera 52A # 3-10', 71, 20, 3.42080000, -76.55430000, 7, 3, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Siloé sector La Estrella', NOW(), NOW()),
(8, 'SOL-20261109-0008', 'Diego Fernando', 'Palacios Lerma', '3139988776', 'Calle 121 # 23-45', 75, 21, 3.43220000, -76.47130000, 8, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Desepaz etapa 3', NOW(), NOW()),
(9, 'SOL-20261109-0009', 'Luz Marina', 'Gómez de Pance', '3117766554', 'Km 12 Vía La Vorágine, Finca El Recuerdo', 83, 23, 3.33420000, -76.55240000, 1, 2, CURRENT_TIMESTAMP, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'RADICADA', 'Zona rural Pance La Vorágine', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
