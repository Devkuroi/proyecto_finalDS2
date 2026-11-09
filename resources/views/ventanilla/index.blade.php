@extends('layouts.app')

@section('title', 'Ventanilla de Radicación y Georreferenciación')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="ventanillaApp()"
     x-init="initApp()">

    <!-- Cabecera de la Sede Operativa -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-cali-blue text-white flex items-center justify-center text-2xl shadow">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-xl font-bold text-slate-900">Módulo de Radicación en Ventanilla</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Req 2 & 3 Activo
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Operador: <strong class="text-slate-700">{{ $usuario->nombre }}</strong> •
                    Sede: <strong class="text-cali-blue">{{ $puntoAtencionPredeterminado ? $puntoAtencionPredeterminado->nombre : 'Oficina Central' }}</strong> (Comuna {{ $puntoAtencionPredeterminado->comuna_numero ?? 'N/D' }})
                </p>
            </div>
        </div>

        <!-- Indicador de Política Institucional de 15 Días -->
        <div class="flex items-center space-x-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5">
            <i class="fa-solid fa-clock text-amber-600 text-xl"></i>
            <div class="text-xs">
                <div class="font-bold text-amber-900">POLÍTICA INSTITUCIONAL DE 15 DÍAS</div>
                <div class="text-amber-700">Toda radicación calcula fecha límite estricta (+15 días calendario)</div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Radicado Exitoso -->
    <div x-show="modalExito" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl mb-4 shadow-inner">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900">¡Solicitud Radicada con Éxito!</h3>
                <p class="text-xs text-slate-500 mt-1">El predio ha sido georreferenciado e indexado en la bitácora institucional.</p>

                <!-- Detalle del Radicado -->
                <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200 text-left space-y-2 text-xs">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                        <span class="text-slate-500 font-medium">Número de Radicado:</span>
                        <span class="font-mono font-bold text-sm text-cali-blue bg-blue-50 px-2 py-0.5 rounded border border-blue-200" x-text="ultimoRadicado.radicado_codigo"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Solicitante:</span>
                        <span class="font-semibold text-slate-800" x-text="ultimoRadicado.solicitante"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dirección:</span>
                        <span class="font-semibold text-slate-800" x-text="ultimoRadicado.direccion"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Comuna / Barrio:</span>
                        <span class="font-semibold text-slate-800"><span x-text="ultimoRadicado.comuna_corregimiento"></span> - <span x-text="ultimoRadicado.barrio"></span></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Coordenadas GPS:</span>
                        <span class="font-mono text-slate-700" x-text="ultimoRadicado.latitud + ', ' + ultimoRadicado.longitud"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Fecha Límite (15 Días):</span>
                        <span class="font-bold text-rose-600" x-text="ultimoRadicado.fecha_limite + ' (15 días restantes)'"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200">
                        <span class="text-slate-500">Estado Inicial:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">RADICADA</span>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="button" @click="modalExito = false"
                        class="w-full py-2.5 px-4 rounded-xl bg-cali-blue hover:bg-cali-darkblue text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-plus mr-1.5"></i> Radicar Siguiente Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Principal: Formulario RIA + Mapa Leaflet Interactivo de Cali -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Columna Izquierda: Formulario de Radicación con Selectores Dependientes (Req 2 & 3) -->
        <div class="lg:col-span-5 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-file-signature text-cali-blue text-lg"></i>
                    <h2 class="text-base font-bold text-slate-900">Formulario de Solicitud</h2>
                </div>
                <span class="text-xs text-slate-400 font-mono">* Campos obligatorios</span>
            </div>

            <!-- Alertas de Error en Formulario -->
            <div x-show="errorMessage" x-cloak class="p-3 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <div class="font-bold flex items-center space-x-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Verifique los campos</span>
                </div>
                <p x-text="errorMessage" class="mt-1"></p>
            </div>

            <form @submit.prevent="guardarSolicitud()" class="space-y-4">
                
                <!-- Nombres y Apellidos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nombres Solicitante *
                        </label>
                        <input type="text" x-model="form.nombres_solicitante" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                            placeholder="Ej. Juan Carlos">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Apellidos Solicitante *
                        </label>
                        <input type="text" x-model="form.apellidos_solicitante" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                            placeholder="Ej. Restrepo Osorio">
                    </div>
                </div>

                <!-- Celular y Dirección -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Celular de Contacto *
                        </label>
                        <div class="relative">
                            <input type="tel" x-model="form.celular" required pattern="[0-9]{7,15}"
                                class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                                placeholder="Ej. 3151234567">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Punto de Atención (Sede) *
                        </label>
                        <select x-model="form.punto_atencion_id" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 bg-slate-50 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition">
                            @foreach($puntosAtencion as $pto)
                                <option value="{{ $pto->id }}" {{ $pto->id == ($puntoAtencionPredeterminado->id ?? 1) ? 'selected' : '' }}>
                                    {{ $pto->nombre }} {{ $pto->es_principal ? '★ (Principal)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Dirección del Predio a Encuestar *
                    </label>
                    <input type="text" x-model="form.direccion" required
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                        placeholder="Ej. Calle 13B # 85-10, Casa 2 / Km 12 Vía Vorágine">
                </div>

                <!-- Selectores Dependientes de Territorio (Cali: Comunas 1-22 y 15 Corregimientos) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">
                            1. Comuna / Corregimiento *
                        </label>
                        <select x-model="form.comuna_corregimiento_id" @change="cargarBarrios()" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 bg-white focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition">
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
                        <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                            <span>2. Barrio / Vereda *</span>
                            <span x-show="cargandoBarrios" x-cloak class="text-[10px] text-cali-blue">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </label>
                        <select x-model="form.barrio_id" :disabled="!form.comuna_corregimiento_id || cargandoBarrios" required
                            class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 bg-white focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition disabled:bg-slate-100 disabled:text-slate-400">
                            <option value="">-- Seleccione barrio/vereda --</option>
                            <template x-for="b in barriosDisponibles" :key="b.id">
                                <option :value="b.id" x-text="b.nombre + ' (' + b.tipo + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Georreferenciación Automática (Capturada desde el Mapa Leaflet) -->
                <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-cali-blue flex items-center">
                            <i class="fa-solid fa-location-crosshairs mr-1.5 text-cali-blue"></i>
                            Georreferenciación en Cali (Requisito 3)
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                              :class="tieneCoordenadas ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                              x-text="tieneCoordenadas ? '✓ Coordenadas Capturadas' : '⚠ Haga clic en el mapa'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[11px] text-slate-600 font-medium">Latitud:</span>
                            <input type="number" step="0.00000001" x-model="form.latitud" required readonly
                                class="w-full text-xs font-mono rounded-lg border border-blue-200 px-2 py-1.5 bg-white text-slate-800">
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-600 font-medium">Longitud:</span>
                            <input type="number" step="0.00000001" x-model="form.longitud" required readonly
                                class="w-full text-xs font-mono rounded-lg border border-blue-200 px-2 py-1.5 bg-white text-slate-800">
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1.5">
                        <i class="fa-solid fa-hand-pointer mr-1 text-slate-400"></i>
                        Haga clic sobre el mapa a la derecha para posicionar el predio. Puede arrastrar el pin si lo necesita.
                    </p>
                </div>

                <!-- Observaciones -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Observaciones Institucionales (Opcional)
                    </label>
                    <textarea x-model="form.observaciones" rows="2"
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                        placeholder="Detalles de acceso, referencias físicas o condición de vulnerabilidad..."></textarea>
                </div>

                <!-- Botón de Envío -->
                <div class="pt-2">
                    <button type="submit" :disabled="guardando || !tieneCoordenadas"
                        class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-md transition disabled:bg-slate-300 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
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
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-map-location-dot text-cali-blue text-lg"></i>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Georreferenciador Territorial de Cali</h2>
                            <span class="text-[11px] text-slate-500">Centrado en coordenadas oficiales [3.4516, -76.5320]</span>
                        </div>
                    </div>

                    <!-- Controles Rápidos del Mapa -->
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="centrarEnCali()" title="Centrar en Cali"
                            class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition flex items-center space-x-1">
                            <i class="fa-solid fa-crosshairs"></i>
                            <span class="hidden sm:inline">Centrar Cali</span>
                        </button>
                        <button type="button" @click="centrarEnSede()" title="Centrar en mi Sede"
                            class="px-2.5 py-1 text-xs rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white transition flex items-center space-x-1">
                            <i class="fa-solid fa-building"></i>
                            <span class="hidden sm:inline">Mi Sede</span>
                        </button>
                    </div>
                </div>

                <!-- Leyenda de Marcadores -->
                <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-600 mb-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-800">Convenciones:</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5 border border-white shadow-sm"></span>Sede Principal (C17)</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-sky-600 mr-1.5 border border-white shadow-sm"></span>Puntos de Atención (C1, 6, 7, 13, 14, 20, 21)</span>
                    <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-rose-600 mr-1.5 border border-white shadow-sm"></span>Predio Seleccionado</span>
                </div>

                <!-- Contenedor del Mapa Leaflet -->
                <div id="mapaVentanilla" class="w-full h-[460px] rounded-xl border border-slate-300 shadow-inner z-10"></div>
            </div>

            <!-- Tabla de Solicitudes Radicadas Recientemente en esta Sesión/Sede -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-cali-blue"></i>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Últimas Solicitudes Radicadas en Esta Sede (Hoy)
                        </h3>
                    </div>
                    <span class="text-xs font-bold text-cali-blue bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200"
                          x-text="solicitudesRecientes.length + ' registradas'"></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2 px-3">Radicado</th>
                                <th class="py-2 px-3">Solicitante</th>
                                <th class="py-2 px-3">Comuna / Barrio</th>
                                <th class="py-2 px-3">Dirección</th>
                                <th class="py-2 px-3">Fecha Límite</th>
                                <th class="py-2 px-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="item in solicitudesRecientes" :key="item.id">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-2 px-3 font-mono font-bold text-cali-blue" x-text="item.radicado_codigo"></td>
                                    <td class="py-2 px-3 font-semibold text-slate-800" x-text="item.nombres_solicitante + ' ' + item.apellidos_solicitante"></td>
                                    <td class="py-2 px-3">
                                        <span x-text="item.comuna_corregimiento ? item.comuna_corregimiento.nombre : 'Comuna ' + item.comuna_corregimiento_id"></span>
                                        <span class="text-slate-400">/</span>
                                        <span class="text-slate-700" x-text="item.barrio ? item.barrio.nombre : ''"></span>
                                    </td>
                                    <td class="py-2 px-3 truncate max-w-[150px]" :title="item.direccion" x-text="item.direccion"></td>
                                    <td class="py-2 px-3 font-semibold text-rose-600">
                                        <span x-text="item.fecha_limite ? item.fecha_limite.substring(0, 10) : '+15 días'"></span>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800" x-text="item.estado"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="solicitudesRecientes.length === 0">
                                <td colspan="6" class="text-center py-4 text-slate-400 text-xs">
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
            // Centro oficial de Cali
            const caliCenter = [3.4516, -76.5320];
            this.map = L.map('mapaVentanilla').setView(caliCenter, 13);

            // Capa de tiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Alcaldía de Cali',
                maxZoom: 19
            }).addTo(this.map);

            // Iconos personalizados
            const sedePrincipalIcon = L.divIcon({
                className: 'custom-pin-principal',
                html: '<div style="background-color: #D4AF37; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.3); color: #002244; font-size: 14px;"><i class="fa-solid fa-star"></i></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            const sedeComunaIcon = L.divIcon({
                className: 'custom-pin-sede',
                html: '<div style="background-color: #003366; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 3px 5px rgba(0,0,0,0.25); color: #ffffff; font-size: 11px;"><i class="fa-solid fa-building"></i></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // Graficar los 8 Puntos de Atención Oficiales de Cali (Comunas 1, 6, 7, 13, 14, 20, 21 y Sede Principal C17)
            this.puntosAtencion.forEach(pto => {
                const icon = pto.es_principal ? sedePrincipalIcon : sedeComunaIcon;
                const marker = L.marker([pto.latitud, pto.longitud], { icon: icon }).addTo(this.map);
                marker.bindPopup(`
                    <div class="text-xs p-1">
                        <strong class="text-cali-blue block font-bold">${pto.nombre}</strong>
                        <div class="text-slate-600 mt-1"><i class="fa-solid fa-location-dot mr-1"></i>${pto.direccion}</div>
                        <div class="text-slate-500 mt-0.5"><i class="fa-solid fa-phone mr-1"></i>${pto.telefono || 'Sin teléfono'}</div>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold ${pto.es_principal ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'}">
                            ${pto.es_principal ? 'Sede Principal (Oficina Central)' : 'Punto de Atención Comuna ' + pto.comuna_numero}
                        </span>
                    </div>
                `);
            });

            // Marcador inicial del predio a encuestar
            this.actualizarMarcadorPredio(this.form.latitud, this.form.longitud);

            // Evento: Clic en cualquier punto del mapa para georreferenciar
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
                html: '<div style="background-color: #DE350B; width: 32px; height: 32px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; border: 3px solid #ffffff; box-shadow: 0 4px 8px rgba(0,0,0,0.4);"><i class="fa-solid fa-house text-white text-xs" style="transform: rotate(45deg);"></i></div>',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            });

            if (this.predioMarker) {
                this.predioMarker.setLatLng([lat, lng]);
            } else {
                this.predioMarker = L.marker([lat, lng], {
                    icon: predioIcon,
                    draggable: true
                }).addTo(this.map);

                // Evento al arrastrar el pin
                this.predioMarker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    this.form.latitud = parseFloat(position.lat.toFixed(8));
                    this.form.longitud = parseFloat(position.lng.toFixed(8));
                });
            }

            this.predioMarker.bindPopup(`
                <div class="text-xs p-1">
                    <strong class="text-rose-700 block font-bold">Ubicación del Predio Georreferenciado</strong>
                    <div class="text-slate-600 mt-1">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Arrastre el marcador para ajustar la posición exacta.</div>
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
            try {
                const res = await fetch(`/api/territorio/barrios?comuna_id=${this.form.comuna_corregimiento_id}`);
                const data = await res.json();
                if (data.success) {
                    this.barriosDisponibles = data.data;
                    this.form.barrio_id = this.barriosDisponibles.length > 0 ? this.barriosDisponibles[0].id : '';

                    // Si el primer barrio tiene coordenadas de referencia, mover el mapa sutilmente hacia allá
                    if (this.barriosDisponibles.length > 0 && this.barriosDisponibles[0].latitud_referencia) {
                        const b = this.barriosDisponibles[0];
                        this.map.panTo([b.latitud_referencia, b.longitud_referencia]);
                        this.form.latitud = b.latitud_referencia;
                        this.form.longitud = b.longitud_referencia;
                        this.actualizarMarcadorPredio(b.latitud_referencia, b.longitud_referencia);
                    }
                }
            } catch (err) {
                console.error('Error cargando barrios:', err);
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

                // Éxito en radicación
                this.ultimoRadicado = result.data;
                this.modalExito = true;

                // Agregar a la tabla de solicitudes recientes en pantalla
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

                // Limpiar formulario para la siguiente radicación
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
