@extends('layouts.app')

@section('title', 'Panel de Usuario - ' . $rolNombre)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Tarjeta de Identidad y Sesión Autenticada (Demostración Req 1) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            <div class="flex items-center space-x-5">
                <div class="w-14 h-14 rounded-lg bg-slate-900 text-cali-gold flex items-center justify-center text-2xl shadow-sm border border-slate-700">
                    @if($rolNombre === 'ADMINISTRADOR')
                        <i class="fa-solid fa-user-gear"></i>
                    @elseif($rolNombre === 'ENCUESTADOR')
                        <i class="fa-solid fa-clipboard-user"></i>
                    @elseif($rolNombre === 'CALIDAD')
                        <i class="fa-solid fa-award"></i>
                    @else
                        <i class="fa-solid fa-user-shield"></i>
                    @endif
                </div>

                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-black text-slate-900">{{ $usuario->nombre }}</h1>
                        <span class="px-2.5 py-1 rounded-md text-xs font-black uppercase tracking-wider bg-slate-900 text-amber-400 border border-amber-400/30">
                            {{ $rolNombre }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-700 font-medium mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span><i class="fa-solid fa-envelope mr-1 text-slate-500"></i>{{ $usuario->email }}</span>
                        <span><i class="fa-solid fa-id-card mr-1 text-slate-500"></i>CC: {{ $usuario->identificacion }}</span>
                        <span><i class="fa-solid fa-building mr-1 text-slate-500"></i>{{ $usuario->puntoAtencion ? $usuario->puntoAtencion->nombre : 'Oficina Central de Cali' }}</span>
                        <span class="inline-flex items-center text-emerald-700 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5"></span>
                            Sesión activa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Accesos de navegación si es administrador -->
            @if($rolNombre === 'ADMINISTRADOR')
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ventanilla.index') }}" class="min-h-[44px] px-4 py-2.5 rounded-lg bg-cali-blue hover:bg-cali-darkblue text-white text-xs font-bold transition shadow-sm inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <i class="fa-solid fa-desktop"></i>
                    <span>Ventanilla</span>
                </a>
                <a href="{{ route('enrutador.bitacora') }}" class="min-h-[44px] px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <i class="fa-solid fa-route"></i>
                    <span>Bitácora</span>
                </a>
            </div>
            @endif

        </div>
    </div>

    <!-- Mensaje del perfil -->
    <div class="bg-slate-50 border-l-4 border-slate-600 rounded-r-xl p-4 mb-6 border border-slate-200">
        <p class="text-xs text-slate-700 leading-relaxed font-medium">
            Módulo en desarrollo para el perfil actual. Las opciones operativas estarán disponibles en la siguiente fase (Sprint 2).
        </p>
    </div>

    <!-- Módulos Planificados para este Rol -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="text-sm font-bold text-slate-900">
                    Módulos en planificación
                </span>
            </div>
            <span class="text-xs font-semibold px-2.5 py-0.5 bg-slate-200 text-slate-700 rounded">
                Sprint 2
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($infoModuloSprint2['modulos_sprint_2'] as $mod)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200 hover:border-cali-blue transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-cali-blue flex items-center justify-center text-base mb-3">
                        @if($mod['icono'] === 'map')
                            <i class="fa-solid fa-map-location-dot"></i>
                        @elseif($mod['icono'] === 'clipboard')
                            <i class="fa-solid fa-clipboard-check"></i>
                        @elseif($mod['icono'] === 'chart')
                            <i class="fa-solid fa-chart-pie"></i>
                        @elseif($mod['icono'] === 'document')
                            <i class="fa-solid fa-file-pdf"></i>
                        @elseif($mod['icono'] === 'check-circle')
                            <i class="fa-solid fa-circle-check"></i>
                        @elseif($mod['icono'] === 'bell')
                            <i class="fa-solid fa-bell"></i>
                        @else
                            <i class="fa-solid fa-cubes"></i>
                        @endif
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1.5">{{ $mod['nombre'] }}</h4>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $mod['desc'] }}</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Estado en Jira:</span>
                    <span class="font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">Sprint 2 Backlog</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Resumen del Sistema -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-chart-simple text-cali-blue"></i>
            <span>Resumen general</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-700 font-semibold block mb-1">Puntos de Atención Oficiales</span>
                <span class="text-2xl font-black text-cali-blue">{{ $stats['total_sedes'] }} Sedes</span>
                <span class="text-xs text-slate-600 block mt-0.5">C17 Principal + C1,6,7,13,14,20,21</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-700 font-semibold block mb-1">Solicitudes Radicadas Hoy</span>
                <span class="text-2xl font-black text-emerald-700">{{ $stats['total_solicitudes_hoy'] }}</span>
                <span class="text-xs text-slate-600 block mt-0.5">Bitácora activa</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-700 font-semibold block mb-1">Usuarios Institucionales</span>
                <span class="text-2xl font-black text-purple-700">{{ $stats['total_usuarios'] }}</span>
                <span class="text-xs text-slate-600 block mt-0.5">5 Perfiles habilitados</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-xs text-slate-700 font-semibold block mb-1">Total Solicitudes Registradas</span>
                <span class="text-2xl font-black text-slate-900">{{ $stats['total_historico'] }}</span>
                <span class="text-xs text-slate-600 block mt-0.5">Base de datos MySQL</span>
            </div>
        </div>
    </div>

</div>
@endsection
