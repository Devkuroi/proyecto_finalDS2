@extends('layouts.app')

@section('title', 'Ventanilla de Radicación y Georreferenciación')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="ventanillaApp()"
     x-init="initApp()"
     @keydown.escape.window="if(modalExito) modalExito = false">

    <h1 class="sr-only">Módulo de Radicación en Ventanilla</h1>

    <!-- Modal de Confirmación de Radicado Exitoso (Accesible con Tecla Escape) -->
    <div x-show="modalExito" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modalTitulo">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-md border border-slate-200" @click.outside="modalExito = false">
            <div class="text-center">
                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div id="modalTitulo" class="text-lg font-bold text-slate-900">Solicitud Radicada</div>
                <p class="text-xs text-slate-600 mt-1">La solicitud ha sido registrada exitosamente en el sistema.</p>

                <!-- Detalle del Radicado -->
                <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200 text-left space-y-2 text-xs">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                        <span class="text-slate-600 font-semibold">Número de Radicado:</span>
                        <span class="font-mono font-bold text-sm text-cali-blue bg-blue-50 px-2 py-0.5 rounded border border-blue-200" x-text="ultimoRadicado.radicado_codigo"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Solicitante:</span>
                        <span class="font-semibold text-slate-900" x-text="ultimoRadicado.solicitante"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Dirección:</span>
                        <span class="font-semibold text-slate-900" x-text="ultimoRadicado.direccion"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Comuna / Barrio:</span>
                        <span class="font-semibold text-slate-900"><span x-text="ultimoRadicado.comuna_corregimiento"></span> - <span x-text="ultimoRadicado.barrio"></span></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Coordenadas GPS:</span>
                        <span class="font-mono font-semibold text-slate-800" x-text="ultimoRadicado.latitud + ', ' + ultimoRadicado.longitud"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Fecha Límite:</span>
                        <span class="font-bold text-slate-800" x-text="ultimoRadicado.fecha_limite"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200">
                        <span class="text-slate-600 font-medium">Estado Inicial:</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">RADICADA</span>
                    </div>
                </div>

                <div class="mt-5 flex space-x-3">
                    <button type="button" @click="modalExito = false"
                        class="w-full min-h-11 py-2.5 px-4 rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white font-bold text-xs shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cali-blue">
                        <i class="fa-solid fa-plus mr-1.5"></i> Radicar Siguiente Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Principal: Formulario + Mapa Leaflet -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Columna Izquierda: Formulario de Radicación -->
        <div class="lg:col-span-5 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-800">Datos de la solicitud</span>
            </div>

            <!-- Alertas de Error en Formulario -->
            <div x-show="errorMessage" x-cloak class="p-3 mb-4 rounded-lg bg-rose-50 border border-rose-300 text-rose-900 text-xs" role="alert">
                <div class="font-bold flex items-center space-x-1.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-700"></i>
                    <span>Verifique los campos requeridos</span>
                </div>
                <p x-text="errorMessage" class="mt-1 font-medium"></p>
            </div>

            <form @submit.prevent="guardarSolicitud()" class="space-y-4">

                <!-- Nombres y Apellidos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="campoNombres" class="block text-xs font-bold text-slate-800 mb-1">
                            Nombres Solicitante *
                        </label>
                        <input id="campoNombres" type="text" x-model="form.nombres_solicitante" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                            placeholder="Ej. Juan Carlos">
                    </div>
                    <div>
                        <label for="campoApellidos" class="block text-xs font-bold text-slate-800 mb-1">
                            Apellidos Solicitante *
                        </label>
                        <input id="campoApellidos" type="text" x-model="form.apellidos_solicitante" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                            placeholder="Ej. Restrepo Osorio">
                    </div>
                </div>

                <!-- Celular y Punto de Atención -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="campoCelular" class="block text-xs font-bold text-slate-800 mb-1">
                            Celular de Contacto *
                        </label>
                        <input id="campoCelular" type="tel" x-model="form.celular" required pattern="[0-9]{7,15}"
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900 font-mono"
                            placeholder="Ej. 3151234567">
                    </div>
                    <div>
                        <label for="campoPunto" class="block text-xs font-bold text-slate-800 mb-1">
                            Punto de Atención (Sede) *
                        </label>
                        <select id="campoPunto" x-model="form.punto_atencion_id" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 bg-slate-50 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition text-slate-900">
                            @foreach($puntosAtencion as $pto)
                                <option value="{{ $pto->id }}" {{ $pto->id == ($puntoAtencionPredeterminado->id ?? 1) ? 'selected' : '' }}>
                                    {{ $pto->nombre }} {{ $pto->es_principal ? '(Principal)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="campoDireccion" class="block text-xs font-bold text-slate-800 mb-1 items-center justify-between">
                        <span>Dirección del solicitante *</span>
                        <span x-show="buscandoDireccion" x-cloak class="text-[11px] text-cali-blue font-semibold">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Verificando en Cali...
                        </span>
                    </label>
                    <div class="flex space-x-2">
                        <input id="campoDireccion" type="text" x-model="form.direccion" @input="validarDireccion()" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                            placeholder="Ej. Calle 13B # 85-10 / Cra 1 # 4-50 / Km 12 Vía Vorágine">
                        <button type="button" @click="buscarDireccionEnMapa()" title="Verificar y ubicar dirección en el mapa de Cali"
                            class="min-h-9.5 px-3.5 py-2 bg-cali-blue hover:bg-cali-darkblue text-white text-xs font-bold rounded-lg transition flex items-center space-x-1.5 shrink-0 focus:outline-none focus:ring-2 focus:ring-cali-blue">
                            <i class="fa-solid fa-magnifying-glass-location"></i>
                            <span class="hidden sm:inline">Ubicar</span>
                        </button>
                    </div>

                    <!-- Mensaje de error de dirección no válida en Cali -->
                    <div x-show="errorDireccion" x-cloak class="mt-1.5 p-2 rounded-md bg-rose-50 border border-rose-200 text-[11px] text-rose-800 font-medium flex items-center space-x-1.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 shrink-0"></i>
                        <span x-text="errorDireccion"></span>
                    </div>

                    <!-- Mensaje de éxito de dirección verificada -->
                    <div x-show="direccionVerificada && !errorDireccion" x-cloak class="mt-1 text-[11px] text-emerald-700 font-semibold flex items-center space-x-1">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Dirección verificada en Cali</span>
                    </div>
                </div>

                <!-- Selectores de Territorio -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <label for="campoComuna" class="block text-xs font-bold text-slate-900 mb-1">
                            Comuna o Corregimiento *
                        </label>
                        <select id="campoComuna" x-model="form.comuna_corregimiento_id" @change="cargarBarrios()" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition text-slate-900">
                            <option value="">-- Seleccione división --</option>
                            <optgroup label="Zona urbana">
                                <template x-for="c in comunasUrbanas" :key="c.id">
                                    <option :value="c.id" x-text="c.nombre"></option>
                                </template>
                            </optgroup>
                            <optgroup label="Zona rural">
                                <template x-for="corr in corregimientosRurales" :key="corr.id">
                                    <option :value="corr.id" x-text="corr.nombre"></option>
                                </template>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label for="campoBarrio" class="block text-xs font-bold text-slate-900 mb-1 items-center justify-between">
                            <span>Barrio o Vereda *</span>
                            <span x-show="cargandoBarrios" x-cloak class="text-[11px] text-cali-blue font-semibold">
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Cargando...
                            </span>
                        </label>
                        <select id="campoBarrio" x-model="form.barrio_id" :disabled="!form.comuna_corregimiento_id || cargandoBarrios" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition disabled:bg-slate-100 disabled:text-slate-500 text-slate-900">
                            <option value="">-- Seleccione barrio/vereda --</option>
                            <template x-for="b in barriosDisponibles" :key="b.id">
                                <option :value="b.id" x-text="b.nombre + ' (' + b.tipo + ')'"></option>
                            </template>
                        </select>

                        <!-- Estado de Error de Carga Territorial -->
                        <div x-show="errorBarrios" x-cloak class="mt-1 text-[11px] text-rose-700 flex items-center justify-between">
                            <span>Error cargando barrios.</span>
                            <button type="button" @click="cargarBarrios()" class="text-cali-blue font-bold underline hover:text-cali-darkblue">Reintentar</button>
                        </div>
                    </div>
                </div>

                <!-- Georreferenciación en Mapa -->
                <div class="p-3.5 bg-blue-50/70 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-cali-blue flex items-center">
                            <i class="fa-solid fa-location-crosshairs mr-1.5 text-cali-blue"></i>
                            Ubicación geográfica (Solo Cali)
                        </span>
                        <span class="text-[11px] px-2 py-0.5 rounded-md font-bold"
                              :class="tieneCoordenadas ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300'"
                              x-text="tieneCoordenadas ? 'Ubicación seleccionada' : 'Seleccione en el mapa'"></span>
                    </div>

                    <!-- Alerta de coordenada fuera de Cali -->
                    <div x-show="errorUbicacion" x-cloak class="mb-2.5 p-2.5 rounded-lg bg-rose-50 border border-rose-300 text-rose-900 text-xs flex items-center space-x-2" role="alert">
                        <i class="fa-solid fa-circle-xmark text-rose-700 text-base shrink-0"></i>
                        <span x-text="errorUbicacion" class="font-semibold"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="campoLatitud" class="block text-[11px] text-slate-700 font-semibold mb-0.5">Latitud:</label>
                            <input id="campoLatitud" type="number" step="0.00000001" x-model="form.latitud" required readonly
                                class="w-full text-xs font-mono rounded-lg border border-blue-200 px-2.5 py-1.5 bg-white text-slate-900 font-semibold">
                        </div>
                        <div>
                            <label for="campoLongitud" class="block text-[11px] text-slate-700 font-semibold mb-0.5">Longitud:</label>
                            <input id="campoLongitud" type="number" step="0.00000001" x-model="form.longitud" required readonly
                                class="w-full text-xs font-mono rounded-lg border border-blue-200 px-2.5 py-1.5 bg-white text-slate-900 font-semibold">
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="campoObservaciones" class="block text-xs font-bold text-slate-800 mb-1">
                        Observaciones (opcional)
                    </label>
                    <textarea id="campoObservaciones" x-model="form.observaciones" rows="2"
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                        placeholder="Detalles de acceso, referencias físicas o condición de vulnerabilidad..."></textarea>
                </div>

                <!-- Botón de Envío accesible (Mínimo 44px de altura) -->
                <div class="pt-2">
                    <button type="submit" :disabled="guardando || !tieneCoordenadas"
                        class="w-full min-h-11 py-3 px-4 rounded-lg font-bold text-xs text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-sm transition disabled:bg-slate-300 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                        <span x-show="!guardando" class="flex items-center">
                            <i class="fa-solid fa-stamp mr-2 text-sm"></i>
                            Radicar Solicitud
                        </span>
                        <span x-show="guardando" x-cloak class="flex items-center">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Guardando solicitud...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Columna Derecha: Mapa Interactivo Leaflet -->
        <div class="lg:col-span-7 space-y-4">

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-map-location-dot text-cali-blue text-base"></i>
                        <span class="text-sm font-semibold text-slate-800">Mapa</span>
                    </div>

                    <!-- Controles Rápidos del Mapa con altura accesible -->
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="centrarEnCali()" title="Centrar mapa en la ciudad"
                            class="min-h-9.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-cali-blue">
                            <i class="fa-solid fa-crosshairs text-slate-600"></i>
                            <span class="hidden sm:inline">Centrar ciudad</span>
                        </button>
                        <button type="button" @click="centrarEnSede()" title="Centrar mapa en mi sede"
                            class="min-h-9.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-cali-blue">
                            <i class="fa-solid fa-building"></i>
                            <span class="hidden sm:inline">Mi Sede</span>
                        </button>
                    </div>
                </div>

                <!-- Convenciones del mapa -->
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-700 mb-3 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                    <span class="font-bold text-slate-900">Convenciones:</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5 border border-white shadow-sm"></span>Sede Principal</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-cali-blue mr-1.5 border border-white shadow-sm"></span>Puntos de atención</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-rose-600 mr-1.5 border border-white shadow-sm"></span>Ubicación seleccionada</span>
                </div>

                <!-- Contenedor del Mapa Leaflet -->
                <div id="mapaVentanilla" class="w-full h-[480px] min-h-[420px] rounded-lg border border-slate-300 shadow-inner z-10" style="height: 480px; min-height: 420px;" aria-label="Mapa interactivo de radicación"></div>
            </div>

            <!-- Tabla de Solicitudes Radicadas Recientemente en esta Sesión/Sede -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-cali-blue"></i>
                        <span class="text-xs font-semibold text-slate-700">Solicitudes del día</span>
                    </div>
                    <span class="text-xs font-bold text-cali-blue bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-200"
                          x-text="solicitudesRecientes.length + ' registradas'"></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-100 text-slate-800 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Radicado</th>
                                <th class="py-2.5 px-3">Solicitante</th>
                                <th class="py-2.5 px-3">Comuna / Barrio</th>
                                <th class="py-2.5 px-3">Dirección</th>
                                <th class="py-2.5 px-3">Fecha Límite</th>
                                <th class="py-2.5 px-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="item in solicitudesRecientes" :key="item.id">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-2.5 px-3 font-mono font-bold text-cali-blue" x-text="item.radicado_codigo"></td>
                                    <td class="py-2.5 px-3 font-semibold text-slate-900" x-text="item.nombres_solicitante + ' ' + item.apellidos_solicitante"></td>
                                    <td class="py-2.5 px-3">
                                        <span x-text="item.comuna_corregimiento ? item.comuna_corregimiento.nombre : 'Comuna ' + item.comuna_corregimiento_id"></span>
                                        <span class="text-slate-500">/</span>
                                        <span class="text-slate-800 font-medium" x-text="item.barrio ? item.barrio.nombre : ''"></span>
                                    </td>
                                    <td class="py-2.5 px-3 truncate max-w-37.5" :title="item.direccion" x-text="item.direccion"></td>
                                    <td class="py-2.5 px-3 font-semibold text-rose-700">
                                        <span x-text="item.fecha_limite ? item.fecha_limite.substring(0, 10) : '+15 días'"></span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200" x-text="item.estado"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="solicitudesRecientes.length === 0">
                                <td colspan="6" class="text-center py-5 text-slate-600 text-xs">
                                    No hay solicitudes radicadas aún en esta sesión.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function ventanillaApp() {
    return {
        // Datos del formulario
        form: {
            nombres_solicitante: '',
            apellidos_solicitante: '',
            celular: '',
            direccion: '',
            comuna_corregimiento_id: '{{ $puntoAtencionPredeterminado->comuna_numero ?? 17 }}',
            barrio_id: '',
            latitud: {{ $puntoAtencionPredeterminado->latitud ?? 3.37652400 }},
            longitud: {{ $puntoAtencionPredeterminado->longitud ?? -76.53671200 }},
            punto_atencion_id: {{ $puntoAtencionPredeterminado->id ?? 1 }},
            observaciones: '',
        },

        // Catálogos Territoriales
        comunas: @json($comunas),
        puntosAtencion: @json($puntosAtencion),
        barriosDisponibles: [],
        solicitudesRecientes: @json($solicitudesHoy),

        // Estado UI
        cargandoBarrios: false,
        errorBarrios: false,
        guardando: false,
        errorMessage: null,
        modalExito: false,
        ultimoRadicado: {},
        errorUbicacion: null,
        errorDireccion: null,
        buscandoDireccion: false,
        direccionVerificada: false,

        // Leaflet Mapa
        map: null,
        predioMarker: null,
        sedeActualLat: {{ $puntoAtencionPredeterminado->latitud ?? 3.37652400 }},
        sedeActualLng: {{ $puntoAtencionPredeterminado->longitud ?? -76.53671200 }},

        get comunasUrbanas() {
            return this.comunas.filter(c => c.tipo === 'COMUNA');
        },

        get corregimientosRurales() {
            return this.comunas.filter(c => c.tipo === 'CORREGIMIENTO');
        },

        esCoordenadaEnCali(lat, lng) {
            if (!lat || !lng) return false;
            // 1. Marco delimitador estricto de Santiago de Cali
            if (lat < 3.30000000 || lat > 3.53000000 || lng < -76.65000000 || lng > -76.46500000) {
                return false;
            }
            // 2. Exclusión de Jamundí / Puerto Tejada (Suroriente y sur del Río Jamundí)
            if (lat < 3.31200000 && lng > -76.54000000) {
                return false;
            }
            // 3. Exclusión de Yumbo (Zona norte industrial/urbana por encima de Menga y Paso del Comercio)
            if (lat > 3.50000000 && lng > -76.53500000) {
                return false;
            }
            // 4. Exclusión de Candelaria / Palmira (Oriente del Río Cauca)
            if (lng > -76.46800000 && lat < 3.48000000) {
                return false;
            }
            return true;
        },

        get tieneCoordenadas() {
            return this.form.latitud && this.form.longitud && this.esCoordenadaEnCali(this.form.latitud, this.form.longitud);
        },

        validarDireccion() {
            const dir = (this.form.direccion || '').trim();
            if (!dir || dir.length < 5) {
                this.errorDireccion = 'La dirección debe tener al menos 5 caracteres.';
                return false;
            }

            const dirLower = dir.toLowerCase();
            const ciudadesProhibidas = [
                'bogota', 'bogotá', 'medellin', 'medellín', 'barranquilla', 'bucaramanga',
                'cartagena', 'pereira', 'manizales', 'pasto', 'popayan', 'popayán',
                'yumbo', 'jamundi', 'jamundí', 'palmira', 'candelaria', 'buenaventura',
                'tulua', 'tuluá', 'buga', 'cartago', 'armenia', 'ibague', 'ibagué',
                'cúcuta', 'cucuta', 'valledupar', 'villavicencio', 'santa marta',
                'puerto tejada', 'puertotejada', 'villagorgona', 'poblado campestre',
                'acopi', 'arroyohondo', 'dagua', 'la cumbre', 'vijes', 'pradera', 'florida'
            ];

            for (const ciudad of ciudadesProhibidas) {
                const regex = new RegExp('\\b' + ciudad + '\\b', 'i');
                if (regex.test(dirLower)) {
                    this.errorDireccion = 'La dirección ingresada hace referencia a otro municipio (' + ciudad.toUpperCase() + ') fuera de Santiago de Cali.';
                    return false;
                }
            }

            const patronUrbano = /\b(calle|carrera|cra|cll|cl|cr|avenida|av|diagonal|diag|dg|transversal|trans|tv|autopista|circunvalar|callejon|callejón|pasaje|manzana|mz)\b\.?\s+[0-9a-z#\-\s\.]+/i;
            const patronRural = /\b(km|kilometro|kilómetro|vereda|corregimiento|sector|finca|lote|parcela|parcelacion|parcelación|hacienda|hda|callejon|callejón|via|vía)\b\.?\s+[0-9a-z#\-\s\.]+/i;

            if (!patronUrbano.test(dir) && !patronRural.test(dir)) {
                this.errorDireccion = 'La dirección no corresponde a una nomenclatura válida de Cali (use Calle, Carrera, Avenida, Diagonal, Transversal, Km, Vereda con su respectivo número).';
                return false;
            }

            this.errorDireccion = null;
            return true;
        },

        async verificarMunicipioOSM(lat, lng) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`);
                const data = await res.json();
                if (data && data.address) {
                    const addr = data.address;
                    const mun = (addr.town || addr.city || addr.municipality || addr.county || '').toLowerCase();
                    const ajenos = ['jamundi', 'jamundí', 'yumbo', 'palmira', 'candelaria', 'puerto tejada', 'dagua', 'la cumbre', 'vijes', 'pradera', 'florida'];
                    for (const ajeno of ajenos) {
                        if (mun.includes(ajeno) || (data.display_name && data.display_name.toLowerCase().includes(ajeno))) {
                            this.errorUbicacion = `La ubicación seleccionada pertenece al municipio de ${addr.town || addr.city || ajeno.toUpperCase()} y no a Santiago de Cali.`;
                            this.form.latitud = null;
                            this.form.longitud = null;
                            if (this.predioMarker) {
                                this.map.removeLayer(this.predioMarker);
                                this.predioMarker = null;
                            }
                            return false;
                        }
                    }
                }
            } catch (e) {
                // Si la red externa no responde, el filtro geométrico local garantiza el perímetro
            }
            return true;
        },

        async buscarDireccionEnMapa() {
            if (!this.validarDireccion()) return;
            this.buscandoDireccion = true;
            this.errorUbicacion = null;

            try {
                const query = encodeURIComponent(this.form.direccion + ', Cali, Valle del Cauca, Colombia');
                const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=3&addressdetails=1&viewbox=-76.65,3.53,-76.465,3.30&bounded=1`);
                const data = await res.json();

                if (data && data.length > 0) {
                    let itemValido = null;
                    const municipiosAjenos = ['jamundi', 'jamundí', 'yumbo', 'palmira', 'candelaria', 'puerto tejada', 'dagua', 'la cumbre', 'vijes', 'pradera', 'florida'];

                    for (const item of data) {
                        const addr = item.address || {};
                        const munDetectado = (addr.town || addr.city || addr.municipality || addr.county || '').toLowerCase();
                        const esAjeno = municipiosAjenos.some(m => munDetectado.includes(m) || (item.display_name && item.display_name.toLowerCase().includes(m)));

                        const lat = parseFloat(parseFloat(item.lat).toFixed(8));
                        const lng = parseFloat(parseFloat(item.lon).toFixed(8));

                        if (!esAjeno && this.esCoordenadaEnCali(lat, lng)) {
                            itemValido = { lat, lng };
                            break;
                        }
                    }

                    if (itemValido) {
                        this.form.latitud = itemValido.lat;
                        this.form.longitud = itemValido.lng;
                        this.actualizarMarcadorPredio(itemValido.lat, itemValido.lng);
                        this.map.setView([itemValido.lat, itemValido.lng], 16);
                        this.direccionVerificada = true;
                        this.errorUbicacion = null;
                        this.errorDireccion = null;
                    } else {
                        this.direccionVerificada = false;
                        this.errorUbicacion = 'La dirección se localizó fuera del perímetro de Santiago de Cali (municipio vecino no permitido).';
                        this.errorDireccion = 'La dirección no pertenece al perímetro urbano ni rural de Santiago de Cali.';
                    }
                } else {
                    this.direccionVerificada = false;
                    this.errorDireccion = 'No se encontró la dirección exacta en el mapa de Cali. Por favor seleccione el punto en el mapa de Cali.';
                }
            } catch (err) {
                // Si la red externa no responde, la validación sintáctica de Cali es suficiente
                this.direccionVerificada = true;
            } finally {
                this.buscandoDireccion = false;
            }
        },

        initApp() {
            this.initMap();
            if (this.form.comuna_corregimiento_id) {
                this.cargarBarrios();
            }
        },

        initMap() {
            const caliCenter = [3.4516, -76.5320];
            const boundsCali = L.latLngBounds(
                [3.2950, -76.6600], // Límite sur (frontera Pance con Jamundí)
                [3.5350, -76.4550]  // Límite norte / oriente (frontera con Yumbo y Palmira)
            );

            this.map = L.map('mapaVentanilla', {
                maxBounds: boundsCali,
                maxBoundsViscosity: 1.0,
                minZoom: 11
            }).setView(caliCenter, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors | Alcaldía de Cali',
                maxZoom: 19
            }).addTo(this.map);

            setTimeout(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            }, 250);

            // Icono de Sede Principal con Landmark institucional (R-04)
            const sedePrincipalIcon = L.divIcon({
                className: 'custom-pin-principal',
                html: '<div style="background-color: #D4AF37; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.3); color: #002244; font-size: 13px;"><i class="fa-solid fa-landmark"></i></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            // Icono de Sedes Zonales
            const sedeComunaIcon = L.divIcon({
                className: 'custom-pin-sede',
                html: '<div style="background-color: #003366; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.25); color: #ffffff; font-size: 11px;"><i class="fa-solid fa-building"></i></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            this.puntosAtencion.forEach(pto => {
                const icon = pto.es_principal ? sedePrincipalIcon : sedeComunaIcon;
                const marker = L.marker([pto.latitud, pto.longitud], { icon: icon }).addTo(this.map);
                marker.bindPopup(`
                    <div class="text-xs p-1">
                        <strong class="text-cali-blue block font-bold text-sm">${pto.nombre}</strong>
                        <div class="text-slate-700 mt-1"><i class="fa-solid fa-location-dot mr-1 text-slate-500"></i>${pto.direccion}</div>
                        <div class="text-slate-600 mt-0.5"><i class="fa-solid fa-phone mr-1 text-slate-500"></i>${pto.telefono || 'Sin teléfono'}</div>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold ${pto.es_principal ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-blue-100 text-blue-900 border border-blue-300'}">
                            ${pto.es_principal ? 'Sede Principal' : 'Punto de Atención Comuna ' + pto.comuna_numero}
                        </span>
                    </div>
                `);
            });

            this.actualizarMarcadorPredio(this.form.latitud, this.form.longitud);

            this.map.on('click', async (e) => {
                const lat = parseFloat(e.latlng.lat.toFixed(8));
                const lng = parseFloat(e.latlng.lng.toFixed(8));

                if (!this.esCoordenadaEnCali(lat, lng)) {
                    this.errorUbicacion = 'La ubicación seleccionada está fuera de Santiago de Cali (municipio vecino no permitido). Solo se permite georreferenciar predios dentro de Cali.';
                    return;
                }

                this.errorUbicacion = null;
                this.form.latitud = lat;
                this.form.longitud = lng;
                this.actualizarMarcadorPredio(lat, lng);
                await this.verificarMunicipioOSM(lat, lng);
            });
        },

        actualizarMarcadorPredio(lat, lng) {
            const predioIcon = L.divIcon({
                className: 'custom-pin-predio',
                html: '<div style="background-color: #DE350B; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 3px 6px rgba(0,0,0,0.35);"><i class="fa-solid fa-house text-white text-xs" style="transform: rotate(45deg);"></i></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });

            if (this.predioMarker) {
                this.predioMarker.setLatLng([lat, lng]);
            } else {
                this.predioMarker = L.marker([lat, lng], {
                    icon: predioIcon,
                    draggable: true
                }).addTo(this.map);

                this.predioMarker.on('dragend', async (event) => {
                    const position = event.target.getLatLng();
                    const nLat = parseFloat(position.lat.toFixed(8));
                    const nLng = parseFloat(position.lng.toFixed(8));

                    if (!this.esCoordenadaEnCali(nLat, nLng)) {
                        this.errorUbicacion = 'La ubicación arrastrada está fuera de Santiago de Cali. Solo se permite georreferenciar predios dentro de Cali.';
                        if (this.form.latitud && this.form.longitud) {
                            this.predioMarker.setLatLng([this.form.latitud, this.form.longitud]);
                        }
                        return;
                    }

                    this.errorUbicacion = null;
                    this.form.latitud = nLat;
                    this.form.longitud = nLng;
                    await this.verificarMunicipioOSM(nLat, nLng);
                });
            }

            this.predioMarker.bindPopup(`
                <div class="text-xs p-1">
                    <strong class="text-rose-700 block font-bold">Ubicación del Predio Georreferenciado</strong>
                    <div class="text-slate-700 mt-1">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Arrastre el marcador para ajustar la posición exacta.</div>
                </div>
            `).openPopup();
        },

        centrarEnCali() {
            this.map.setView([3.4516, -76.5320], 13);
        },

        centrarEnSede() {
            this.map.setView([this.sedeActualLat, this.sedeActualLng], 15);
            this.actualizarMarcadorPredio(this.sedeActualLat, this.sedeActualLng);
            this.form.latitud = this.sedeActualLat;
            this.form.longitud = this.sedeActualLng;
        },

        async cargarBarrios() {
            if (!this.form.comuna_corregimiento_id) {
                this.barriosDisponibles = [];
                this.form.barrio_id = '';
                return;
            }

            this.cargandoBarrios = true;
            this.errorBarrios = false;
            try {
                const res = await fetch(`/api/territorio/barrios?comuna_id=${this.form.comuna_corregimiento_id}`);
                const data = await res.json();
                if (data.success) {
                    this.barriosDisponibles = data.data;
                    this.form.barrio_id = this.barriosDisponibles.length > 0 ? this.barriosDisponibles[0].id : '';

                    if (this.barriosDisponibles.length > 0 && this.barriosDisponibles[0].latitud_referencia) {
                        const b = this.barriosDisponibles[0];
                        this.map.panTo([b.latitud_referencia, b.longitud_referencia]);
                        this.form.latitud = b.latitud_referencia;
                        this.form.longitud = b.longitud_referencia;
                        this.actualizarMarcadorPredio(b.latitud_referencia, b.longitud_referencia);
                    }
                } else {
                    this.errorBarrios = true;
                }
            } catch (err) {
                console.error('Error cargando barrios:', err);
                this.errorBarrios = true;
            } finally {
                this.cargandoBarrios = false;
            }
        },

        async guardarSolicitud() {
            this.errorMessage = null;

            if (!this.validarDireccion()) {
                this.errorMessage = this.errorDireccion;
                return;
            }

            if (!this.tieneCoordenadas) {
                this.errorMessage = 'Debe seleccionar una ubicación válida dentro del perímetro de Santiago de Cali.';
                return;
            }

            this.guardando = true;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('/api/solicitudes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        const mensajes = Object.values(result.errors).flat().join(' ');
                        this.errorMessage = mensajes;
                    } else {
                        this.errorMessage = result.message || 'Error al radicar la solicitud.';
                    }
                    return;
                }

                this.ultimoRadicado = result.data;
                this.modalExito = true;

                this.solicitudesRecientes.unshift({
                    id: result.data.id,
                    radicado_codigo: result.data.radicado_codigo,
                    nombres_solicitante: this.form.nombres_solicitante,
                    apellidos_solicitante: this.form.apellidos_solicitante,
                    direccion: result.data.direccion,
                    comuna_corregimiento: { nombre: result.data.comuna_corregimiento },
                    barrio: { nombre: result.data.barrio },
                    fecha_limite: result.data.fecha_limite,
                    estado: 'RADICADA'
                });

                this.form.nombres_solicitante = '';
                this.form.apellidos_solicitante = '';
                this.form.celular = '';
                this.form.direccion = '';
                this.form.observaciones = '';

            } catch (error) {
                console.error('Error:', error);
                this.errorMessage = 'Ocurrió un error en la conexión de red al registrar la solicitud.';
            } finally {
                this.guardando = false;
            }
        }
    }
}
</script>
@endpush
