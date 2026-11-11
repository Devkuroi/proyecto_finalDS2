@extends('layouts.app')

@section('title', 'Bitácora Diaria del Enrutador')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="bitacoraApp()"
     x-init="initBitacora()">

    <!-- Encabezado de la Bitácora Diaria (Req 4) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg bg-cali-darkblue text-cali-gold flex items-center justify-center text-xl shadow-sm">
                <i class="fa-solid fa-route"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-xl font-bold text-slate-900">Bitácora Diaria del Enrutador</h1>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-0.5">
                    Coordinador Logístico: <strong class="text-slate-800">{{ $usuario->nombre }}</strong> •
                    Consolidación de Solicitudes Diarias para Santiago de Cali (Zonas Urbanas y Rurales)
                </p>
            </div>
        </div>

        <!-- Selector de Fecha y Botón de Recarga accesible (min 44px) -->
        <div class="flex items-center space-x-2 bg-slate-50 p-2 rounded-lg border border-slate-200">
            <div class="flex items-center space-x-1.5 text-xs text-slate-700">
                <i class="fa-solid fa-calendar-day text-cali-blue"></i>
                <label for="filtroFecha" class="font-bold">Fecha:</label>
            </div>
            <input id="filtroFecha" type="date" x-model="filtros.fecha" @change="consultarBitacora()"
                class="min-h-[40px] text-xs font-semibold rounded-lg border border-slate-300 px-3 py-1.5 bg-white text-slate-900 focus:ring-2 focus:ring-cali-blue">
            <button type="button" @click="consultarBitacora()" :disabled="cargando"
                class="min-h-[40px] min-w-[40px] p-2 rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white transition disabled:opacity-50 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-amber-400"
                title="Actualizar Datos de Bitácora"
                aria-label="Actualizar Datos de Bitácora">
                <i class="fa-solid fa-rotate" :class="cargando ? 'fa-spin' : ''"></i>
            </button>
        </div>
    </div>

    <!-- Tarjetas de Métricas Consolidadas del Día (KPIs) con alto contraste -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        
        <!-- Total Solicitudes Hoy -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between text-slate-600 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider">Total Día</span>
                <i class="fa-solid fa-clipboard-list text-cali-blue"></i>
            </div>
            <div class="text-2xl font-black text-slate-900" x-text="metricas.total">0</div>
            <div class="text-xs text-slate-600 font-medium mt-1">Todas las sedes consolidadas</div>
        </div>

        <!-- Cobertura Urbana (Comunas 1-22) -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between text-slate-600 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider">Zona Urbana</span>
                <i class="fa-solid fa-city text-emerald-700"></i>
            </div>
            <div class="text-2xl font-black text-emerald-700" x-text="metricas.urbanas">0</div>
            <div class="text-xs text-slate-600 font-medium mt-1">Comunas 1 a 22</div>
        </div>

        <!-- Cobertura Rural (15 Corregimientos) -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between text-slate-600 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider">Zona Rural</span>
                <i class="fa-solid fa-mountain-sun text-amber-700"></i>
            </div>
            <div class="text-2xl font-black text-amber-700" x-text="metricas.rurales">0</div>
            <div class="text-xs text-slate-600 font-medium mt-1">Corregimientos de Cali</div>
        </div>

        <!-- Vigentes en Política (≤ 15 Días) -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between text-slate-600 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider">En Tiempo</span>
                <i class="fa-solid fa-clock text-sky-700"></i>
            </div>
            <div class="text-2xl font-black text-sky-700" x-text="metricas.vigentes">0</div>
            <div class="text-xs text-slate-600 font-medium mt-1">Política 15 días activa</div>
        </div>

        <!-- Vencidas (> 15 Días) -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-slate-600 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider">Vencidas</span>
                <i class="fa-solid fa-triangle-exclamation text-rose-700"></i>
            </div>
            <div class="text-2xl font-black text-rose-700" x-text="metricas.vencidas">0</div>
            <div class="text-xs text-slate-600 font-medium mt-1">> 15 días sin enrutar</div>
        </div>

    </div>

    <!-- Filtros de Búsqueda y Consolidación por Sede -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Barra de Filtros -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
                
                <!-- Filtro por Punto de Atención (Sedes Oficiales) -->
                <div>
                    <label for="filtroSede" class="block text-xs font-bold text-slate-800 uppercase mb-1">Punto de Atención (Sede)</label>
                    <select id="filtroSede" x-model="filtros.punto_atencion_id" @change="consultarBitacora()"
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 bg-slate-50 focus:ring-2 focus:ring-cali-blue text-slate-900">
                        <option value="">-- Todos los Puntos de Atención (8 Sedes) --</option>
                        @foreach($puntosAtencion as $pto)
                            <option value="{{ $pto->id }}">
                                {{ $pto->nombre }} {{ $pto->es_principal ? '(Principal)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Zona -->
                <div>
                    <label for="filtroZona" class="block text-xs font-bold text-slate-800 uppercase mb-1">Zona Geográfica</label>
                    <select id="filtroZona" x-model="filtros.zona" @change="consultarBitacora()"
                        class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 bg-slate-50 focus:ring-2 focus:ring-cali-blue text-slate-900">
                        <option value="">-- Urbana y Rural --</option>
                        <option value="URBANA">Solo Zona Urbana (Comunas)</option>
                        <option value="RURAL">Solo Zona Rural (Corregimientos)</option>
                    </select>
                </div>

                <!-- Buscador de Texto Libre -->
                <div>
                    <label for="filtroTexto" class="block text-xs font-bold text-slate-800 uppercase mb-1">Búsqueda Rápida</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input id="filtroTexto" type="text" x-model="busquedaTexto"
                            class="w-full pl-8 pr-3 py-2 text-xs rounded-lg border border-slate-300 bg-white placeholder-slate-500 text-slate-900 focus:ring-2 focus:ring-cali-blue"
                            placeholder="Radicado, nombre, dirección...">
                    </div>
                </div>

            </div>

            <!-- Resumen de Sedes en Línea -->
            <div class="text-right border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-4 text-xs">
                <span class="text-slate-600 font-semibold">Sedes Oficiales Integradas:</span>
                <div class="font-bold text-slate-900">Comunas 1, 6, 7, 13, 14, 17, 20 y 21</div>
            </div>

        </div>
    </div>

    <!-- Sección de Mapa Consolidado y Tabla en Tiempo Real -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mb-6">

        <!-- Mapa Consolidado de la Bitácora (Santiago de Cali) -->
        <div class="lg:col-span-12 bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-map text-cali-blue text-base"></i>
                    <span class="text-sm font-semibold text-slate-800">Mapa consolidado</span>
                </div>

                <!-- Leyenda del Mapa con Icono Landmark en Sede Principal -->
                <div class="hidden sm:flex items-center space-x-4 text-xs text-slate-700">
                    <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5 border border-white"></span>Sede Principal C17</span>
                    <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-cali-blue mr-1.5 border border-white"></span>Sedes Zonales</span>
                    <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-600 mr-1.5 border border-white"></span>Solicitud Vigente (1-15 días)</span>
                </div>
            </div>

            <!-- Mapa Leaflet -->
            <div id="mapaBitacora" class="w-full h-[400px] rounded-lg border border-slate-300 shadow-inner z-10" aria-label="Mapa consolidado de solicitudes de encuestas en Cali"></div>
        </div>

        <!-- Tabla Detallada de la Bitácora Diaria (Accesible por Teclado: Tab, Enter, Space) -->
        <div class="lg:col-span-12 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-4 border-b border-slate-100 gap-2">
                <div>
                    <span class="text-xs text-slate-600 font-medium">
                        Mostrando <strong x-text="solicitudesFiltradas.length"></strong> registros
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700" aria-label="Tabla de bitácora diaria de solicitudes">
                    <thead class="bg-slate-100 text-slate-800 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-3">Radicado</th>
                            <th class="py-3 px-3">Hora</th>
                            <th class="py-3 px-3">Solicitante</th>
                            <th class="py-3 px-3">Teléfono</th>
                            <th class="py-3 px-3">Comuna / Corregimiento</th>
                            <th class="py-3 px-3">Barrio / Vereda</th>
                            <th class="py-3 px-3">Dirección Predio</th>
                            <th class="py-3 px-3">Sede de Radicación</th>
                            <th class="py-3 px-3 text-center">Días Restantes</th>
                            <th class="py-3 px-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="item in solicitudesFiltradas" :key="item.id">
                            <!-- Fila accesible por teclado (R-32) -->
                            <tr class="hover:bg-slate-50 transition cursor-pointer focus:outline-none focus:bg-blue-50 focus:ring-2 focus:ring-inset focus:ring-cali-blue"
                                tabindex="0"
                                role="button"
                                @click="enfocarEnMapa(item)"
                                @keydown.enter="enfocarEnMapa(item)"
                                @keydown.space.prevent="enfocarEnMapa(item)"
                                :aria-label="'Ver ubicación de ' + item.radicado_codigo + ' en el mapa'">
                                <td class="py-3 px-3 font-mono font-bold text-cali-blue">
                                    <span class="bg-blue-50 px-2 py-0.5 rounded border border-blue-200" x-text="item.radicado_codigo"></span>
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-700" x-text="item.hora_radicacion"></td>
                                <td class="py-3 px-3 font-semibold text-slate-900" x-text="item.solicitante"></td>
                                <td class="py-3 px-3 font-mono text-slate-700" x-text="item.celular"></td>
                                <td class="py-3 px-3">
                                    <span class="font-medium text-slate-900" x-text="item.comuna_nombre"></span>
                                    <span class="block text-[10px]" :class="item.zona === 'RURAL' ? 'text-amber-800 font-bold' : 'text-slate-500'" x-text="item.zona"></span>
                                </td>
                                <td class="py-3 px-3 font-medium text-slate-800" x-text="item.barrio_nombre"></td>
                                <td class="py-3 px-3 text-slate-700 truncate max-w-[180px]" :title="item.direccion" x-text="item.direccion"></td>
                                <td class="py-3 px-3">
                                    <span class="text-[11px] font-semibold text-slate-900" x-text="item.punto_atencion_nombre"></span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2 py-1 rounded-md text-[11px] font-bold"
                                          :class="item.dias_restantes > 5 ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-rose-100 text-rose-900 border border-rose-300'"
                                          x-text="item.dias_restantes + ' días'"></span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200"
                                          x-text="item.estado"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="solicitudesFiltradas.length === 0">
                            <td colspan="10" class="text-center py-8 text-slate-600">
                                <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-400 block"></i>
                                No se encontraron solicitudes que coincidan con la fecha o filtros seleccionados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function bitacoraApp() {
    return {
        filtros: {
            fecha: '{{ $fechaDefecto }}',
            punto_atencion_id: '',
            zona: '',
        },
        busquedaTexto: '',
        cargando: false,
        solicitudes: [],
        metricas: {
            total: 0,
            urbanas: 0,
            rurales: 0,
            vigentes: 0,
            vencidas: 0
        },
        puntosAtencion: @json($puntosAtencion),
        mapa: null,
        markersLayer: null,

        get solicitudesFiltradas() {
            if (!this.busquedaTexto.trim()) {
                return this.solicitudes;
            }
            const q = this.busquedaTexto.toLowerCase();
            return this.solicitudes.filter(s =>
                s.radicado_codigo.toLowerCase().includes(q) ||
                s.solicitante.toLowerCase().includes(q) ||
                s.direccion.toLowerCase().includes(q) ||
                s.barrio_nombre.toLowerCase().includes(q) ||
                s.comuna_nombre.toLowerCase().includes(q)
            );
        },

        initBitacora() {
            this.initMapa();
            this.consultarBitacora();
        },

        initMapa() {
            const caliCenter = [3.4516, -76.5320];
            this.mapa = L.map('mapaBitacora').setView(caliCenter, 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors | Alcaldía de Cali',
                maxZoom: 19
            }).addTo(this.mapa);

            this.markersLayer = L.layerGroup().addTo(this.mapa);

            // Iconos de Sedes Oficiales con Landmark en Sede Principal (R-04)
            const sedePrincipalIcon = L.divIcon({
                className: 'pin-sede-principal',
                html: '<div style="background-color: #D4AF37; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.3); color: #002244; font-size: 13px;"><i class="fa-solid fa-landmark"></i></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            const sedeComunaIcon = L.divIcon({
                className: 'pin-sede-comuna',
                html: '<div style="background-color: #003366; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.25); color: #ffffff; font-size: 11px;"><i class="fa-solid fa-building"></i></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            this.puntosAtencion.forEach(pto => {
                const icon = pto.es_principal ? sedePrincipalIcon : sedeComunaIcon;
                L.marker([pto.latitud, pto.longitud], { icon: icon }).addTo(this.mapa)
                    .bindPopup(`
                        <div class="text-xs p-1">
                            <strong class="text-cali-blue block font-bold text-sm">${pto.nombre}</strong>
                            <div class="text-slate-700 mt-1">${pto.direccion}</div>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold ${pto.es_principal ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-blue-100 text-blue-900 border border-blue-300'}">
                                ${pto.es_principal ? 'Sede Principal C17 (Oficina Central)' : 'Punto de Atención Comuna ' + pto.comuna_numero}
                            </span>
                        </div>
                    `);
            });
        },

        async consultarBitacora() {
            this.cargando = true;
            try {
                const params = new URLSearchParams(this.filtros);
                const response = await fetch(`/api/enrutador/bitacora-diaria?${params.toString()}`);
                const data = await response.json();

                if (data.success) {
                    this.solicitudes = data.data;
                    this.metricas = data.metricas;
                    this.actualizarMapaPines();
                }
            } catch (err) {
                console.error('Error al consultar bitácora diaria:', err);
            } finally {
                this.cargando = false;
            }
        },

        actualizarMapaPines() {
            this.markersLayer.clearLayers();

            const bounds = [];

            this.solicitudes.forEach(s => {
                if (s.latitud && s.longitud) {
                    bounds.push([s.latitud, s.longitud]);

                    const solicitudIcon = L.divIcon({
                        className: 'pin-solicitud',
                        html: `<div style="background-color: ${s.zona === 'RURAL' ? '#D97706' : '#059669'}; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.3); color: #ffffff; font-size: 10px;"><i class="fa-solid fa-house"></i></div>`,
                        iconSize: [22, 22],
                        iconAnchor: [11, 11]
                    });

                    const marker = L.marker([s.latitud, s.longitud], { icon: solicitudIcon }).addTo(this.markersLayer);
                    marker.bindPopup(`
                        <div class="text-xs p-1">
                            <span class="font-mono font-bold text-cali-blue text-xs block">${s.radicado_codigo}</span>
                            <div class="font-bold text-slate-900 mt-1">${s.solicitante}</div>
                            <div class="text-slate-700">${s.direccion}</div>
                            <div class="text-slate-600 text-[11px] mt-0.5">${s.comuna_nombre} - ${s.barrio_nombre} (${s.zona})</div>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-200">
                                <span class="font-bold text-rose-700">${s.dias_restantes} días restantes</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-900 border border-blue-200">${s.estado}</span>
                            </div>
                        </div>
                    `);
                }
            });

            if (bounds.length > 0) {
                this.mapa.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
            }
        },

        enfocarEnMapa(solicitud) {
            if (solicitud.latitud && solicitud.longitud) {
                this.mapa.setView([solicitud.latitud, solicitud.longitud], 16);
                window.scrollTo({ top: 300, behavior: 'smooth' });
            }
        }
    }
}
</script>
@endpush
