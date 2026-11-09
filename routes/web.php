<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TerritorioController;
use App\Http\Controllers\VentanillaController;
use App\Http\Controllers\EnrutadorController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Gestión y Georreferenciación de Encuestas Cali
| Entrega 1 (Req 1, 2, 3, 4 + Precarga de Sedes Req 5)
|--------------------------------------------------------------------------
*/

// Redirección inicial
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ------------------------------------------------------------------------
// MÓDULO DE AUTENTICACIÓN (Req 1)
// ------------------------------------------------------------------------
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Endpoints REST de Autenticación
Route::prefix('api/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
});

// ------------------------------------------------------------------------
// CATÁLOGO TERRITORIAL DE CALI Y PUNTOS DE ATENCIÓN (Req 2 y Req 5)
// ------------------------------------------------------------------------
Route::prefix('api')->group(function () {
    Route::get('/territorio/comunas-corregimientos', [TerritorioController::class, 'getComunasCorregimientos'])->name('api.territorio.comunas');
    Route::get('/territorio/barrios', [TerritorioController::class, 'getBarrios'])->name('api.territorio.barrios');
    Route::get('/puntos-atencion', [TerritorioController::class, 'getPuntosAtencion'])->name('api.puntos_atencion');
});

// ------------------------------------------------------------------------
// RUTAS AUTENTICADAS
// ------------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Dashboard General / Redirección inteligente y Vistas Placeholder de perfiles Entrega 2
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --------------------------------------------------------------------
    // MÓDULO DE RADICACIÓN EN VENTANILLA (Req 2 y 3) - Rol: VENTANILLA o ADMINISTRADOR
    // --------------------------------------------------------------------
    Route::middleware(['role:VENTANILLA,ADMINISTRADOR'])->group(function () {
        Route::get('/ventanilla', [VentanillaController::class, 'index'])->name('ventanilla.index');
        Route::post('/api/solicitudes', [VentanillaController::class, 'storeSolicitud'])->name('api.solicitudes.store');
        Route::get('/api/solicitudes/recientes', [VentanillaController::class, 'solicitudesRecientes'])->name('api.solicitudes.recientes');
    });

    // --------------------------------------------------------------------
    // MÓDULO DE BITÁCORA DIARIA DEL ENRUTADOR (Req 4) - Rol: ENRUTADOR o ADMINISTRADOR
    // --------------------------------------------------------------------
    Route::middleware(['role:ENRUTADOR,ADMINISTRADOR'])->group(function () {
        Route::get('/enrutador/bitacora', [EnrutadorController::class, 'index'])->name('enrutador.bitacora');
        Route::get('/api/enrutador/bitacora-diaria', [EnrutadorController::class, 'bitacoraDiaria'])->name('api.enrutador.bitacora');
    });
});
