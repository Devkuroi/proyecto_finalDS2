<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión y Georreferenciación de Encuestas') | Alcaldía de Cali</title>

    <!-- Tailwind CSS CDN -->
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
        .leaflet-popup-content-wrapper { border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased">

    <!-- Barra Superior Institucional -->
    <header class="bg-gradient-to-r from-cali-darkblue via-cali-blue to-sky-900 text-white shadow-md z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Marca / Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center border border-white/20">
                        <i class="fa-solid fa-map-location-dot text-cali-gold text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-lg tracking-tight leading-tight flex items-center space-x-2">
                            <span>SISTEMA DE ENCUESTAS CALI</span>
                            <span class="bg-amber-400 text-slate-900 text-xs px-2 py-0.5 rounded-full font-extrabold uppercase tracking-wider">Entrega 1</span>
                        </div>
                        <div class="text-xs text-sky-200">Santiago de Cali • Alcaldía Distrital • Zonas Urbanas y Rurales</div>
                    </div>
                </div>

                <!-- Usuario Autenticado / Sesión -->
                @auth
                <div class="flex items-center space-x-4" x-data="{ userMenuOpen: false }">
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->nombre }}</span>
                        <div class="flex items-center justify-end space-x-1.5 text-xs text-sky-200">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="font-bold text-cali-gold">{{ Auth::user()->rol->nombre }}</span>
                            @if(Auth::user()->puntoAtencion)
                                <span>• {{ Auth::user()->puntoAtencion->nombre }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Enlaces rápidos según perfil -->
                    <div class="flex items-center space-x-2">
                        @if(Auth::user()->esVentanilla() || Auth::user()->esAdministrador())
                            <a href="{{ route('ventanilla.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md bg-white/10 hover:bg-white/20 text-white transition flex items-center space-x-1 border border-white/10">
                                <i class="fa-solid fa-desktop"></i>
                                <span class="hidden sm:inline">Ventanilla</span>
                            </a>
                        @endif

                        @if(Auth::user()->esEnrutador() || Auth::user()->esAdministrador())
                            <a href="{{ route('enrutador.bitacora') }}" class="px-3 py-1.5 text-xs font-medium rounded-md bg-white/10 hover:bg-white/20 text-white transition flex items-center space-x-1 border border-white/10">
                                <i class="fa-solid fa-route"></i>
                                <span class="hidden sm:inline">Bitácora Enrutador</span>
                            </a>
                        @endif

                        @if(Auth::user()->esAdministrador() || Auth::user()->esEncuestador() || Auth::user()->esCalidad())
                            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-xs font-medium rounded-md bg-white/10 hover:bg-white/20 text-white transition flex items-center space-x-1 border border-white/10">
                                <i class="fa-solid fa-id-badge"></i>
                                <span class="hidden sm:inline">Mi Panel</span>
                            </a>
                        @endif

                        <!-- Botón de Cerrar Sesión -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Cerrar Sesión" class="p-2 text-sky-200 hover:text-white hover:bg-red-500/80 rounded-md transition duration-150">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Alertas Flash -->
    @if(session('success') || session('error') || session('info'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3">
        @if(session('success'))
            <div class="p-4 mb-3 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-3 text-sm text-rose-800 rounded-lg bg-rose-50 border border-rose-200 flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 mb-3 text-sm text-sky-800 rounded-lg bg-sky-50 border border-sky-200 flex items-center space-x-2">
                <i class="fa-solid fa-circle-info text-sky-600 text-lg"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>
    @endif

    <!-- Contenido Principal -->
    <main class="flex-1 overflow-y-auto">
        @yield('content')
    </main>

    <!-- Footer Institucional -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-3 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-slate-300">Santiago de Cali</span>
                <span>•</span>
                <span>Gestión de Encuestas Domiciliarias (Comunas 1-22 y 15 Corregimientos)</span>
            </div>
            <div class="flex items-center space-x-4 text-slate-400">
                <span>Alcance: <strong class="text-amber-400">Entrega 1 (Req 1, 2, 3, 4 + Sedes Req 5)</strong></span>
                <span>•</span>
                <span>PHP 8.2+ / Laravel / MySQL / Leaflet.js</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
