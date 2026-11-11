# Dirección de Diseño Institucional: Sistema de Encuestas Cali
**Archivo de Dirección:** `DESIGN.md`  
**Institución:** Alcaldía Distrital de Santiago de Cali - Gestión Territorial y Domiciliaria  
**Diales antislop:** `ENERGY 1 / RHYTHM 1 / MOTION 1`  
**Estándar de Accesibilidad:** WCAG 2.1 Nivel AA  

---

## 1. Identidad y Propósito

El Sistema de Gestión y Georreferenciación de Encuestas Domiciliarias es una herramienta pública de administración gubernamental para los 2.2 millones de habitantes de Santiago de Cali (distribuidos en sus 22 comunas urbanas y 15 corregimientos rurales).

El diseño debe transmitir:
- **Sobriedad y Confianza:** Un portal oficial del Estado, no una startup comercial ni un videojuego.
- **Claridad Funcional:** Prioridad absoluta a la legibilidad de datos, radicación ágil en ventanillas de atención ciudadana y control logístico estricto.
- **Accesibilidad Universal:** Contraste tipográfico garantizado para funcionarios y ciudadanos, compatibilidad completa con navegación por teclado y diseño táctil optimizado para dispositivos móviles.

---

## 2. Declaración de Diales (antislop Dials)

| Dial | Nivel | Justificación Institucional |
| :--- | :---: | :--- |
| **ENERGY** | **1 (Calm)** | Interfaz sobria, funcional y basada en datos (estilo GOV.CO / GOV.UK). Cero estridencias visuales, sin orbes fluorescentes ni fondos con mallas multicolores. |
| **RHYTHM** | **1 (Uniform)** | Estructura predecible y consistente. Formularios de captura a la izquierda, mapas espaciales a la derecha y tablas de datos tabulares inferiores con cabeceras uniformes. |
| **MOTION** | **1 (Subtle / Static)** | Animaciones reducidas a micro-interacciones funcionales (estados de foco, apertura de modales y feedback de carga). Sin rebotes, parallax ni transiciones distractoras. |

---

## 3. Paleta Cromática Institucional y Ratios de Contraste

| Color | Token / Hex | Uso Primario | Contraste sobre Blanco (`#FFFFFF`) | Cumplimiento WCAG |
| :--- | :--- | :--- | :---: | :---: |
| **Azul Cali** | `#003366` | Cabeceras institucionales, botones principales, enlaces | **10.5:1** | AAA (Excelente) |
| **Azul Noche** | `#002244` | Fondos de barra superior, texto de alto énfasis | **14.8:1** | AAA (Excelente) |
| **Oro Cívico** | `#D4AF37` | Acentos heráldicos, insignias de honor, sede principal | **N/A (Fondo oscuro)** | AA sobre `#003366` (5.9:1) |
| **Verde Operativo** | `#00875A` | Solicitudes radicadas, vigencia en tiempo, éxito | **4.7:1** | AA (Aprobado) |
| **Rojo Institucional** | `#DE350B` | Marcador de predio en mapa, alertas de vencimiento | **5.2:1** | AA (Aprobado) |
| **Gris Lectura** | `#475569` (`slate-600`) | Textos secundarios, descripciones, metadatos | **7.0:1** | AAA (Aprobado) |
| **Gris Acento** | `#64748b` (`slate-500`) | Etiquetas muted, fecha y hora | **4.6:1** | AA (Aprobado) |
| **Borde Neutro** | `#e2e8f0` (`slate-200`) | Delimitación estructural de paneles y tablas | N/A | Estructural |

*Nota de antislop:* Se prohíbe el uso de `text-slate-400` (#94a3b8) para texto de lectura sobre fondos claros, ya que su ratio de 2.4:1 incumple la norma de contraste.

---

## 4. Tipografía y Jerarquía

- **Pila Tipográfica:** `system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`. Tipografía nativa, rápida, sin llamadas externas a fuentes de terceros.
- **Tamaño de texto base:** `14px` (0.875rem) en tablas y formularios; `12px` (0.75rem) mínimo para metadatos secundarios siempre con peso seminegrita o color `#475569`.
- **Cifras y Coordenadas:** Fuente monoespaciada (`font-mono`) en códigos de radicado (`SOL-YYYYMMDD-XXXX`), coordenadas GPS y celulares para evitar confusiones de caracteres.

---

## 5. Elevación, Sombras y Bordes

- **Regla de Elevación:** Los paneles no flotan en el vacío. La delimitación se logra mediante bordes precisos (`border border-slate-200`) complementados con sombras sutiles (`shadow-sm`).
- **Radios de Borde Consistentes:**
  - Paneles, tarjetas y modales: `rounded-xl` (12px).
  - Campos de entrada, selects y botones: `rounded-lg` (8px).
  - Insignias de estado (`RADICADA`, `EN_RUTA`): `rounded-md` (6px) con borde coloreado tenue.

---

## 6. Accesibilidad y Ergonomía Móvil

1. **Área mínima de contacto táctil:** Todo botón, selector o enlace debe medir al menos **44px de altura** en dispositivos táctiles (`min-h-[44px]`).
2. **Navegación por Teclado:**
   - Todo elemento interactivo debe recibir foco mediante `Tab`.
   - Diálogos y modales deben cerrarse obligatoriamente con la tecla `Escape`.
   - Filas interactivas en tablas de datos deben operar con `Enter` y `Space`.
   - Anillos de foco explícitos y contrastantes: `focus:ring-2 focus:ring-amber-400 focus:outline-none`.
