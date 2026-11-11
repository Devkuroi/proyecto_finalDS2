@extends('layouts.app')

@section('title', 'Panel de Usuario - ' . $rolNombre)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Mensaje temporal de bienvenida (se oculta automáticamente a los 5 segundos) -->
    <div x-data="{ visible: true }"
         x-init="setTimeout(() => visible = false, 5000)"
         x-show="visible"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="bg-slate-50 border-l-4 border-slate-600 rounded-r-xl p-4 mb-6 border flex items-center justify-between"
         role="status">
        <div class="flex items-center space-x-2.5 text-xs text-slate-700 font-medium">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>Bienvenido al sistema. Su sesión ha sido verificada correctamente.</span>
        </div>
        <button type="button" @click="visible = false" class="text-slate-400 hover:text-slate-600 text-xs px-2 py-1 rounded" title="Cerrar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    @if($rolNombre !== 'ADMINISTRADOR')
    <!-- Tarjeta de Identidad para otros perfiles institucionales -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-5">
                <div class="w-14 h-14 rounded-lg bg-slate-900 text-cali-gold flex items-center justify-center text-2xl shadow-sm border border-slate-700">
                    @if($rolNombre === 'ENCUESTADOR')
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
                        <span><i class="fa-solid fa-building mr-1 text-slate-500"></i>{{ $usuario->puntoAtencion ? $usuario->puntoAtencion->nombre : 'Oficina Central' }}</span>
                        <span class="inline-flex items-center text-emerald-700 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5"></span>
                            Sesión activa
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                <span class="text-xs text-slate-600 block mt-0.5">Red de atención municipal</span>
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
                <span class="text-xs text-slate-600 block mt-0.5">Histórico acumulado</span>
            </div>
        </div>
    </div>

</div>
@endsection
