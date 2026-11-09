@extends('layouts.app')

@section('title', 'Panel de Usuario - ' . $rolNombre)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Tarjeta de Identidad y Sesión Autenticada (Demostración Req 1) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            <div class="flex items-center space-x-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 text-cali-gold flex items-center justify-center text-3xl shadow-lg border border-slate-600">
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
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-slate-900 text-amber-400 border border-amber-400/40 shadow-sm">
                            {{ $rolNombre }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span><i class="fa-solid fa-envelope mr-1 text-slate-400"></i>{{ $usuario->email }}</span>
                        <span><i class="fa-solid fa-id-card mr-1 text-slate-400"></i>CC: {{ $usuario->identificacion }}</span>
                        <span><i class="fa-solid fa-building mr-1 text-slate-400"></i>{{ $usuario->puntoAtencion ? $usuario->puntoAtencion->nombre : 'Oficina Central de Cali' }}</span>
                        <span class="inline-flex items-center text-emerald-600 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                            Sesión Autenticada y Activa (Req 1 OK)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Accesos de navegación si es administrador -->
            @if($rolNombre === 'ADMINISTRADOR')
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ventanilla.index') }}" class="px-3.5 py-2 rounded-xl bg-cali-blue hover:bg-cali-darkblue text-white text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                    <i class="fa-solid fa-desktop"></i>
                    <span>Ir a Ventanilla (Req 2 & 3)</span>
                </a>
                <a href="{{ route('enrutador.bitacora') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                    <i class="fa-solid fa-route"></i>
                    <span>Ir a Bitácora (Req 4)</span>
                </a>
            </div>
            @endif

        </div>
    </div>

    <!-- Banner Informativo del Alcance de Entrega 1 vs Sprint 2 -->
    <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-l-4 border-amber-500 rounded-r-2xl p-5 mb-8 bg-white shadow-sm">
        <div class="flex items-start space-x-3">
            <i class="fa-solid fa-circle-exclamation text-amber-600 text-xl mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    Aviso Institucional de Alcance de la Entrega 1 (Guardrail Estricto)
                </h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Usted ha iniciado sesión exitosamente demostrando que el <strong>Requerimiento 1 (Autenticación de los 5 roles)</strong> está completado y validado.
                    Conforme a las restricciones académicas de alcance, las funcionalidades operativas de campo, auditoría avanzada y hojas de ruta corresponden al <strong>Sprint 2</strong> y no deben programarse en esta entrega.
                </p>
            </div>
        </div>
    </div>

    <!-- Módulos Planificados para el Sprint 2 para este Rol -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Módulos Asignados para Habilitación en Sprint 2 (Product Backlog)
                </h2>
                <p class="text-xs text-slate-500">Funcionalidades planificadas en el tablero Scrum institucional</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-slate-200 text-slate-700 rounded-full">
                Sprint 2 (En Planificación)
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($infoModuloSprint2['modulos_sprint_2'] as $mod)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:border-cali-blue transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-cali-blue flex items-center justify-center text-lg mb-4">
                        @if($mod['icono'] === 'map')
                            <i class="fa-solid fa-map-marked-alt"></i>
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
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">{{ $mod['nombre'] }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $mod['desc'] }}</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Estado en Jira:</span>
                    <span class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Sprint 2 Backlog</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Métricas del Sistema en Entrega 1 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-chart-simple text-cali-blue"></i>
            <span>Estado Global de la Infraestructura Institucional (Entrega 1)</span>
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Puntos de Atención Oficiales</span>
                <span class="text-2xl font-black text-cali-blue">{{ $stats['total_sedes'] }} Sedes</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">C17 Principal + C1,6,7,13,14,20,21</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Solicitudes Radicadas Hoy</span>
                <span class="text-2xl font-black text-emerald-600">{{ $stats['total_solicitudes_hoy'] }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Bitácora activa</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Usuarios Institucionales</span>
                <span class="text-2xl font-black text-purple-600">{{ $stats['total_usuarios'] }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">5 Perfiles habilitados</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Total Solicitudes Registradas</span>
                <span class="text-2xl font-black text-slate-900">{{ $stats['total_historico'] }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Base de datos MySQL</span>
            </div>
        </div>
    </div>

</div>
@endsection
