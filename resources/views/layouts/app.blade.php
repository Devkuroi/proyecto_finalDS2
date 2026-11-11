<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión y Georreferenciación de Encuestas') | Alcaldía de Cali</title>

    <!-- Tailwind CSS CDN con configuración institucional -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cali: {
                            blue: '#003366',
                            darkblue: '#002244',
                            lightblue: '#005599',
                            gold: '#D4AF37',
                            green: '#00875A',
                            red: '#DE350B',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Leaflet.js CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style>
        [x-cloak] { display: none !important; }
        .leaflet-popup-content-wrapper { border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        #mapaVentanilla, #mapaBitacora {
            height: 480px !important;
            min-height: 420px !important;
            width: 100% !important;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased">

    <!-- Barra Superior Institucional -->
    <header class="bg-cali-blue text-white shadow-sm z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-17 py-2">
                <!-- Marca -->
                <div>
                    <div class="font-bold text-base sm:text-lg tracking-tight leading-tight">
                        <span>SISTEMA DE GESTIÓN DE ENCUESTAS</span>
                    </div>
                    <div class="text-xs text-sky-100">Alcaldía Distrital • Registro y Georreferenciación</div>
                </div>

                <!-- Usuario Autenticado / Sesión -->
                @auth
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->nombre }}</span>
                        <div class="flex items-center justify-end space-x-1.5 text-xs text-sky-100">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="font-bold text-cali-gold">{{ Auth::user()->rol->nombre }}</span>
                            @if(Auth::user()->puntoAtencion)
                                <span>• {{ Auth::user()->puntoAtencion->nombre }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Enlaces rápidos según perfil (mínimo 44px de área táctil accesible) -->
                    <nav class="flex items-center space-x-1 sm:space-x-2" aria-label="Navegación principal">
                        @if(Auth::user()->esVentanilla() || Auth::user()->esAdministrador())
                            <a href="{{ route('ventanilla.index') }}"
                               class="min-h-11 px-3.5 py-2 text-xs font-semibold rounded-lg bg-white/10 hover:bg-white/20 text-white transition inline-flex items-center space-x-1.5 border border-white/15 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-cali-darkblue">
                                <i class="fa-solid fa-desktop"></i>
                                <span class="hidden sm:inline">Ventanilla</span>
                            </a>
                        @endif

                        @if(Auth::user()->esEnrutador() || Auth::user()->esAdministrador())
                            <a href="{{ route('enrutador.bitacora') }}"
                               class="min-h-11 px-3.5 py-2 text-xs font-semibold rounded-lg bg-white/10 hover:bg-white/20 text-white transition inline-flex items-center space-x-1.5 border border-white/15 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-cali-darkblue">
                                <i class="fa-solid fa-route"></i>
                                <span class="hidden sm:inline">Bitácora</span>
                            </a>
                        @endif

                        @if(Auth::user()->esAdministrador() || Auth::user()->esEncuestador() || Auth::user()->esCalidad())
                            <a href="{{ route('dashboard') }}"
                               class="min-h-11 px-3.5 py-2 text-xs font-semibold rounded-lg bg-white/10 hover:bg-white/20 text-white transition inline-flex items-center space-x-1.5 border border-white/15 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-cali-darkblue">
                                <i class="fa-solid fa-id-badge"></i>
                                <span class="hidden sm:inline">Mi Panel</span>
                            </a>
                        @endif

                        <!-- Botón de Cerrar Sesión Accesible -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    title="Cerrar Sesión"
                                    aria-label="Cerrar Sesión"
                                    class="min-h-11 min-w-11 p-2 text-sky-100 hover:text-white hover:bg-red-600/80 rounded-lg transition inline-flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-cali-darkblue">
                                <i class="fa-solid fa-right-from-bracket text-base"></i>
                            </button>
                        </form>
                    </nav>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Alertas Flash con contraste WCAG AA -->
    @if(session('success') || session('error') || session('info'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" role="status" aria-live="polite">
        @if(session('success'))
            <div class="p-4 mb-3 text-sm text-emerald-900 rounded-lg bg-emerald-50 border border-emerald-300 flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-700 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-3 text-sm text-rose-900 rounded-lg bg-rose-50 border border-rose-300 flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-rose-700 text-lg"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 mb-3 text-sm text-sky-900 rounded-lg bg-sky-50 border border-sky-300 flex items-center space-x-2">
                <i class="fa-solid fa-circle-info text-sky-700 text-lg"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        @endif
    </div>
    @endif

    <!-- Contenido Principal -->
    <main class="flex-1 overflow-y-auto">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
