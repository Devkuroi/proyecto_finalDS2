@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-slate-100"
     x-data="{
        modoDemo: false,
        email: '{{ old('email', 'ventanilla17@cali.gov.co') }}',
        password: 'Password123!',
        cargando: false,
        setDemo(e, p, autoSubmit = false) {
            this.email = e;
            this.password = p;
            if (autoSubmit) {
                this.$nextTick(() => {
                    this.cargando = true;
                    this.$refs.loginForm.submit();
                });
            }
        }
     }">
    <div class="w-full transition-all duration-200" :class="modoDemo ? 'max-w-xl' : 'max-w-md'">

        <!-- Panel 1: Formulario de Inicio de Sesión Normal -->
        <div x-show="!modoDemo" class="bg-white p-7 sm:p-8 rounded-xl shadow-sm border border-slate-200">
            <!-- Encabezado -->
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-11 h-11 rounded-lg bg-cali-blue text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Iniciar Sesión</h1>
                    <p class="text-xs text-slate-600 font-medium">Sistema de Encuestas Domiciliarias</p>
                </div>
            </div>

            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-rose-50 border border-rose-300 text-rose-900 text-xs" role="alert">
                <div class="font-bold flex items-center space-x-1.5 text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-700"></i>
                    <span>Error de Acceso</span>
                </div>
                <ul class="list-disc list-inside mt-1.5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" x-ref="loginForm" @submit="cargando = true" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Correo Electrónico *
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" x-model="email" required
                            class="block w-full pl-10 pr-3 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                            placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Contraseña *
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" x-model="password" required
                            class="block w-full pl-10 pr-3 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-500 text-slate-900"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center text-xs pt-1">
                    <label class="flex items-center text-slate-700 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-cali-blue focus:ring-cali-blue mr-2 h-4 w-4">
                        Recordar sesión en este equipo
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" :disabled="cargando"
                        class="w-full min-h-11 flex justify-center items-center space-x-2 py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-cali-blue hover:bg-cali-darkblue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cali-blue transition duration-150">
                        <span x-show="!cargando" class="flex items-center">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>Ingresar
                        </span>
                        <span x-show="cargando" x-cloak class="flex items-center">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Verificando credenciales...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Botón para alternar a Modo Demo -->
            <div class="mt-6 pt-5 border-t border-slate-200">
                <button type="button" @click="modoDemo = true"
                    class="w-full min-h-11 py-2.5 px-4 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-300 transition flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-cali-blue">
                    <i class="fa-solid fa-id-card-clip text-cali-blue"></i>
                    <span>Ver perfiles de prueba (Modo Demostración)</span>
                </button>
            </div>
        </div>

        <!-- Panel 2: Modo Demostración (Oculta el Login Normal cuando está activo) -->
        <div x-show="modoDemo" x-cloak class="bg-slate-900 text-white p-6 sm:p-7 rounded-xl shadow-sm border border-slate-700">
            
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-400 text-slate-900">
                        <i class="fa-solid fa-id-card mr-1.5"></i> MODO DEMOSTRACIÓN
                    </span>
                    <span class="text-xs text-slate-400">Acceso rápido</span>
                </div>
            </div>

            <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                Seleccione un perfil para acceder directamente con un solo clic:
            </p>

            <div class="space-y-2.5">
                <!-- Ventanilla Principal -->
                <button type="button" @click="setDemo('ventanilla17@cali.gov.co', 'Password123!', true)"
                    class="w-full min-h-11 text-left p-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-emerald-500 transition flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white group-hover:text-emerald-300">
                                Ventanilla (Sede Principal)
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">ventanilla17@cali.gov.co</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-400 border border-emerald-400/30 px-2 py-0.5 rounded-md">Entrar</span>
                </button>

                <!-- Ventanilla Terrón Colorado -->
                <button type="button" @click="setDemo('ventanilla1@cali.gov.co', 'Password123!', true)"
                    class="w-full min-h-11 text-left p-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-emerald-500 transition flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white group-hover:text-emerald-300">
                                Ventanilla (Terrón Colorado)
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">ventanilla1@cali.gov.co</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-400 border border-emerald-400/30 px-2 py-0.5 rounded-md">Entrar</span>
                </button>

                <!-- Enrutador -->
                <button type="button" @click="setDemo('enrutador@cali.gov.co', 'Password123!', true)"
                    class="w-full min-h-11 text-left p-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-sky-500 transition flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-sky-500/20 text-sky-300 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white group-hover:text-sky-300">
                                Enrutador Logístico
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">enrutador@cali.gov.co</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-sky-400 border border-sky-400/30 px-2 py-0.5 rounded-md">Entrar</span>
                </button>

                <!-- Administrador -->
                <button type="button" @click="setDemo('admin@cali.gov.co', 'Password123!', true)"
                    class="w-full min-h-11 text-left p-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-indigo-500 transition flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white group-hover:text-indigo-300">
                                Administrador
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">admin@cali.gov.co</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-indigo-400 border border-indigo-400/30 px-2 py-0.5 rounded-md">Entrar</span>
                </button>

                <!-- Encuestador -->
                <button type="button" @click="setDemo('encuestador1@cali.gov.co', 'Password123!', true)"
                    class="w-full min-h-11 text-left p-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-teal-500 transition flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white group-hover:text-teal-300">
                                Encuestador
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">encuestador1@cali.gov.co</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-teal-400 border border-teal-400/30 px-2 py-0.5 rounded-md">Entrar</span>
                </button>

                <!-- Calidad -->
                <button type="button" @click="setDemo('calidad@cali.gov.co', 'Password123!', true)"
                    class="w-full min-h-11 text-left p-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-purple-500 transition flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-purple-500/20 text-purple-300 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white group-hover:text-purple-300">
                                Calidad
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">calidad@cali.gov.co</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-purple-400 border border-purple-400/30 px-2 py-0.5 rounded-md">Entrar</span>
                </button>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-800 text-xs text-slate-400 text-center flex items-center justify-between">
                <span>Contraseña de prueba:</span>
                <code class="text-amber-300 font-mono font-bold bg-slate-800 px-2 py-0.5 rounded border border-slate-700">Password123!</code>
            </div>

            <div class="mt-4">
                <button type="button" @click="modoDemo = false"
                    class="w-full min-h-10 py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 hover:text-white transition text-center border border-slate-700">
                    Volver al formulario
                </button>
            </div>
        </div>

    </div>
</div>
@endsection
