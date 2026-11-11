@extends('layouts.app')

@section('title', 'Ventanilla de Radicación y Georreferenciación')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="ventanillaApp()"
     x-init="initApp()"
     @keydown.escape.window="if(modalExito) modalExito = false">

    <!-- Cabecera de la Sede Operativa -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg bg-cali-blue text-white flex items-center justify-center text-xl shadow-sm">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-xl font-bold text-slate-900">Módulo de Radicación en Ventanilla</h1>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-0.5">
                    Operador: <strong class="text-slate-800">{{ $usuario->nombre }}</strong> •
                    Sede: <strong class="text-cali-blue">{{ $puntoAtencionPredeterminado ? $puntoAtencionPredeterminado->nombre : 'Oficina Central' }}</strong> (Comuna {{ $puntoAtencionPredeterminado->comuna_numero ?? 'N/D' }})
                </p>
            </div>
        </div>

        <!-- Indicador de Política Institucional de 15 Días -->
        <div class="flex items-center space-x-3 bg-amber-50 border border-amber-300 rounded-lg px-4 py-2.5">
            <i class="fa-solid fa-clock text-amber-700 text-xl" aria-hidden="true"></i>
            <div class="text-xs">
                <div class="font-bold text-amber-900">POLÍTICA INSTITUCIONAL DE 15 DÍAS</div>
                <div class="text-amber-800 font-medium">Toda radicación calcula fecha límite estricta (+15 días calendario)</div>
            </div>
        </div>
    </div>

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
                <p class="text-xs text-slate-600 mt-1">El predio ha sido georreferenciado e indexado en la bitácora institucional.</p>

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
                        <span class="text-slate-600 font-medium">Fecha Límite (15 Días):</span>
                        <span class="font-bold text-rose-700" x-text="ultimoRadicado.fecha_limite + ' (15 días restantes)'"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200">
                        <span class="text-slate-600 font-medium">Estado Inicial:</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">RADICADA</span>
                    </div>
                </div>

                <div class="mt-5 flex space-x-3">
                    <button type="button" @click="modalExito = false"
                        class="w-full min-h-[44px] py-2.5 px-4 rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white font-bold text-xs shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cali-blue">
                        <i class="fa-solid fa-plus mr-1.5"></i> Radicar Siguiente Solicitud (Esc para cerrar)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Principal: Formulario RIA + Mapa Leaflet Interactivo de Cali -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Columna Izquierda: Formulario de Radicación con Selectores Dependientes (Req 2 & 3) -->
        <div class="lg:col-span-5 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-800">Datos de la solicitud</span>
                <span class="text-xs text-slate-600 font-semibold">* Campos obligatorios</span>
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
                    <label for="campoDireccion" class="block text-xs font-bold text-slate-800 mb-1">
                        Dirección del Predio a Encuestar *
                    </label>
                    <input id="campoDireccion" type="text" x-model="form.direccion" required
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                        placeholder="Ej. Calle 13B # 85-10, Casa 2 / Km 12 Vía Vorágine">
                </div>

                <!-- Selectores Dependientes de Territorio (Cali: Comunas 1-22 y 15 Corregimientos) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <label for="campoComuna" class="block text-xs font-bold text-slate-900 mb-1">
                            1. Comuna / Corregimiento *
                        </label>
                        <select id="campoComuna" x-model="form.comuna_corregimiento_id" @change="cargarBarrios()" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition text-slate-900">
                            <option value="">-- Seleccione división --</option>
                            <optgroup label="ZONA URBANA (Comunas 1 a 22)">
                                <template x-for="c in comunasUrbanas" :key="c.id">
                                    <option :value="c.id" x-text="c.nombre"></option>
                                </template>
                            </optgroup>
                            <optgroup label="ZONA RURAL (Corregimientos de Cali)">
                                <template x-for="corr in corregimientosRurales" :key="corr.id">
                                    <option :value="corr.id" x-text="corr.nombre"></option>
                                </template>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label for="campoBarrio" class="block text-xs font-bold text-slate-900 mb-1 flex items-center justify-between">
                            <span>2. Barrio / Vereda *</span>
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

                <!-- Georreferenciación Automática (Capturada desde el Mapa Leaflet) -->
                <div class="p-3.5 bg-blue-50/70 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-cali-blue flex items-center">
                            <i class="fa-solid fa-location-crosshairs mr-1.5 text-cali-blue"></i>
                            Ubicación geográfica
                        </span>
                        <span class="text-[11px] px-2 py-0.5 rounded-md font-bold"
                              :class="tieneCoordenadas ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300'"
                              x-text="tieneCoordenadas ? '✓ Coordenadas Capturadas' : '⚠ Haga clic en el mapa'"></span>
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
                    <p class="text-xs text-slate-600 mt-2 font-medium">
                        <i class="fa-solid fa-hand-pointer mr-1 text-slate-500"></i>
                        Haga clic sobre el mapa a la derecha para posicionar el predio o arrastre el marcador para afinar.
                    </p>
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="campoObservaciones" class="block text-xs font-bold text-slate-800 mb-1">
                        Observaciones Institucionales (Opcional)
                    </label>
                    <textarea id="campoObservaciones" x-model="form.observaciones" rows="2"
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                        placeholder="Detalles de acceso, referencias físicas o condición de vulnerabilidad..."></textarea>
                </div>

                <!-- Botón de Envío accesible (Mínimo 44px de altura) -->
                <div class="pt-2">
                    <button type="submit" :disabled="guardando || !tieneCoordenadas"
                        class="w-full min-h-[44px] py-3 px-4 rounded-lg font-bold text-xs text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-sm transition disabled:bg-slate-300 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                        <span x-show="!guardando" class="flex items-center">
                            <i class="fa-solid fa-stamp mr-2 text-sm"></i>
                            Radicar Solicitud de Encuesta Domiciliaria
                        </span>
                        <span x-show="guardando" x-cloak class="flex items-center">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Radicando en Base de Datos...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Columna Derecha: Mapa Interactivo Leaflet (Santiago de Cali) -->
        <div class="lg:col-span-7 space-y-4">
            
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-map-location-dot text-cali-blue text-base"></i>
                        <span class="text-sm font-semibold text-slate-800">Mapa</span>
                    </div>

                    <!-- Controles Rápidos del Mapa con altura accesible -->
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="centrarEnCali()" title="Centrar mapa en Cali"
                            class="min-h-[38px] px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-cali-blue">
                            <i class="fa-solid fa-crosshairs text-slate-600"></i>
                            <span class="hidden sm:inline">Centrar Cali</span>
                        </button>
                        <button type="button" @click="centrarEnSede()" title="Centrar mapa en mi sede de radicación"
                            class="min-h-[38px] px-3 py-1.5 text-xs font-semibold rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-cali-blue">
                            <i class="fa-solid fa-building"></i>
                            <span class="hidden sm:inline">Mi Sede</span>
                        </button>
                    </div>
                </div>

                <!-- Leyenda de Marcadores con Icono de Landmark en Sede Principal -->
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-700 mb-3 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                    <span class="font-bold text-slate-900">Convenciones:</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5 border border-white shadow-sm"></span>Sede Principal C17 (Oficina Central)</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-cali-blue mr-1.5 border border-white shadow-sm"></span>Sedes Zonales (C1, 6, 7, 13, 14, 20, 21)</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-rose-600 mr-1.5 border border-white shadow-sm"></span>Predio Seleccionado</span>
                </div>

                <!-- Contenedor del Mapa Leaflet -->
                <div id="mapaVentanilla" class="w-full h-[460px] rounded-lg border border-slate-300 shadow-inner z-10" aria-label="Mapa georreferenciador interactivo de Santiago de Cali"></div>
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
                                    <td class="py-2.5 px-3 truncate max-w-[150px]" :title="item.direccion" x-text="item.direccion"></td>
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

        get tieneCoordenadas() {
            return this.form.latitud && this.form.longitud;
        },

        initApp() {
            this.initMap();
            if (this.form.comuna_corregimiento_id) {
                this.cargarBarrios();
            }
        },

        initMap() {
            const caliCenter = [3.4516, -76.5320];
            this.map = L.map('mapaVentanilla').setView(caliCenter, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors | Alcaldía de Cali',
                maxZoom: 19
            }).addTo(this.map);

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
                            ${pto.es_principal ? 'Sede Principal (Oficina Central)' : 'Punto de Atención Comuna ' + pto.comuna_numero}
                        </span>
                    </div>
                `);
            });

            this.actualizarMarcadorPredio(this.form.latitud, this.form.longitud);

            this.map.on('click', (e) => {
                const lat = parseFloat(e.latlng.lat.toFixed(8));
                const lng = parseFloat(e.latlng.lng.toFixed(8));

                this.form.latitud = lat;
                this.form.longitud = lng;
                this.actualizarMarcadorPredio(lat, lng);
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

                this.predioMarker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    this.form.latitud = parseFloat(position.lat.toFixed(8));
                    this.form.longitud = parseFloat(position.lng.toFixed(8));
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
