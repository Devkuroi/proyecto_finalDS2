@extends('layouts.app')

@section('title', 'Acceso al Sistema Institucional')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-100 to-slate-200"
     x-data="{
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
    <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
        
        <!-- Panel Izquierdo: Formulario de Login -->
        <div class="lg:col-span-7 bg-white p-8 rounded-2xl shadow-xl border border-slate-200 flex flex-col justify-between">
            <div>
                <!-- Encabezado del Formulario -->
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-cali-blue text-cali-gold flex items-center justify-center text-2xl shadow">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Iniciar Sesión</h2>
                        <p class="text-xs text-slate-500">Módulo de Autenticación de Perfiles (Req 1)</p>
                    </div>
                </div>

                @if($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                    <div class="font-semibold flex items-center space-x-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Error de Acceso</span>
                    </div>
                    <ul class="list-disc list-inside mt-1 text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" x-ref="loginForm" @submit="cargando = true" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Correo Electrónico Institucional
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <input type="email" id="email" name="email" x-model="email" required
                                class="block w-full pl-10 pr-3 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                                placeholder="usuario@cali.gov.co">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Contraseña Institucional
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input type="password" id="password" name="password" x-model="password" required
                                class="block w-full pl-10 pr-3 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-cali-blue focus:border-cali-blue transition placeholder-slate-400"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-cali-blue focus:ring-cali-blue mr-2">
                            Recordar sesión en este equipo
                        </label>
                        <span class="text-slate-400">Entrega 1 • Santiago de Cali</span>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="cargando"
                            class="w-full flex justify-center items-center space-x-2 py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-cali-blue hover:bg-cali-darkblue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cali-blue transition duration-150">
                            <span x-show="!cargando"><i class="fa-solid fa-right-to-bracket mr-2"></i>Ingresar al Sistema</span>
                            <span x-show="cargando" x-cloak class="flex items-center">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Autenticando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Información Institucional de Seguridad -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Alcaldía Distrital de Santiago de Cali</span>
                <span>Conexión segura SSL/TLS</span>
            </div>
        </div>

        <!-- Panel Derecho: Selector Rápido "Demo Mode" para Sustentación del Profesor -->
        <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-xl flex flex-col justify-between border border-slate-700">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400 text-slate-900">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i> MODO DEMOSTRACIÓN (1 CLIC)
                    </span>
                    <span class="text-[11px] text-slate-400 font-mono">5 Perfiles</span>
                </div>

                <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                    Haga clic en cualquiera de los 5 perfiles para <strong>autenticarse instantáneamente</strong> y verificar los permisos exigidos en la rúbrica:
                </p>

                <!-- Tarjetas de Acceso Rápido por Perfil -->
                <div class="space-y-2.5">
                    
                    <!-- 1. VENTANILLA (Comuna 17 - Sede Principal) -->
                    <button type="button" @click="setDemo('ventanilla17@cali.gov.co', 'Password123!', true)"
                        class="w-full text-left p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-emerald-500/30 hover:border-emerald-400 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-desktop"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-emerald-300 flex items-center space-x-1.5">
                                    <span>VENTANILLA (Comuna 17 - Principal)</span>
                                    <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.2 rounded">Req 2 & 3</span>
                                </div>
                                <div class="text-[11px] text-slate-400">ventanilla17@cali.gov.co</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-emerald-400 group-hover:translate-x-1 transition transform"></i>
                    </button>

                    <!-- 2. VENTANILLA (CALI 1 - Terrón Colorado) -->
                    <button type="button" @click="setDemo('ventanilla1@cali.gov.co', 'Password123!', true)"
                        class="w-full text-left p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-emerald-500/30 hover:border-emerald-400 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-location-pin"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-emerald-300 flex items-center space-x-1.5">
                                    <span>VENTANILLA (CALI 1 - Terrón Colorado)</span>
                                </div>
                                <div class="text-[11px] text-slate-400">ventanilla1@cali.gov.co</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-emerald-400 group-hover:translate-x-1 transition transform"></i>
                    </button>

                    <!-- 3. ENRUTADOR -->
                    <button type="button" @click="setDemo('enrutador@cali.gov.co', 'Password123!', true)"
                        class="w-full text-left p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-sky-500/30 hover:border-sky-400 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-route"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-sky-300 flex items-center space-x-1.5">
                                    <span>ENRUTADOR (Bitácora Diaria)</span>
                                    <span class="text-[10px] bg-sky-500/20 text-sky-300 px-1.5 py-0.2 rounded">Req 4</span>
                                </div>
                                <div class="text-[11px] text-slate-400">enrutador@cali.gov.co</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-sky-400 group-hover:translate-x-1 transition transform"></i>
                    </button>

                    <!-- 4. ADMINISTRADOR -->
                    <button type="button" @click="setDemo('admin@cali.gov.co', 'Password123!', true)"
                        class="w-full text-left p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-indigo-500/30 hover:border-indigo-400 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-indigo-300 flex items-center space-x-1.5">
                                    <span>ADMINISTRADOR (Control Maestro)</span>
                                </div>
                                <div class="text-[11px] text-slate-400">admin@cali.gov.co</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-indigo-400 group-hover:translate-x-1 transition transform"></i>
                    </button>

                    <!-- 5. ENCUESTADOR -->
                    <button type="button" @click="setDemo('encuestador1@cali.gov.co', 'Password123!', true)"
                        class="w-full text-left p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-teal-500/30 hover:border-teal-400 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-clipboard-user"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-teal-300 flex items-center space-x-1.5">
                                    <span>ENCUESTADOR (Operación Campo)</span>
                                </div>
                                <div class="text-[11px] text-slate-400">encuestador1@cali.gov.co</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-teal-400 group-hover:translate-x-1 transition transform"></i>
                    </button>

                    <!-- 6. CALIDAD -->
                    <button type="button" @click="setDemo('calidad@cali.gov.co', 'Password123!', true)"
                        class="w-full text-left p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-purple-500/30 hover:border-purple-400 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-purple-300 flex items-center space-x-1.5">
                                    <span>CALIDAD (Auditoría Metodológica)</span>
                                </div>
                                <div class="text-[11px] text-slate-400">calidad@cali.gov.co</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-purple-400 group-hover:translate-x-1 transition transform"></i>
                    </button>

                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-700/60 text-[11px] text-slate-400 text-center">
                Contraseña universal de prueba: <code class="text-amber-300 font-mono font-bold bg-slate-800 px-1.5 py-0.5 rounded">Password123!</code>
            </div>
        </div>

    </div>
</div>
@endsection
