<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudEncuesta;
use App\Models\PuntoAtencion;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard general o vista placeholder según el rol autenticado
     */
    public function index(Request $request)
    {
        $usuario = Auth::user()->load(['rol', 'puntoAtencion']);
        $rolNombre = strtoupper($usuario->rol->nombre);

        // Si es Ventanilla o Enrutador, redirigir a su módulo operativo de Entrega 1
        if ($rolNombre === 'VENTANILLA') {
            return redirect()->route('ventanilla.index');
        }

        if ($rolNombre === 'ENRUTADOR') {
            return redirect()->route('enrutador.bitacora');
        }

        // Para perfiles con acceso al dashboard (ADMINISTRADOR, ENCUESTADOR, CALIDAD)
        $stats = [
            'total_sedes' => PuntoAtencion::where('activo', 1)->count(),
            'total_solicitudes_hoy' => SolicitudEncuesta::whereDate('fecha_radicacion', Carbon::today())->count(),
            'total_historico' => SolicitudEncuesta::count(),
            'total_usuarios' => User::count(),
        ];

        return view('dashboard.placeholder', compact('usuario', 'rolNombre', 'stats'));
    }
}
