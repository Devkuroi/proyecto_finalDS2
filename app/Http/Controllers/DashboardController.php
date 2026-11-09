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

        // Para ADMINISTRADOR, ENCUESTADOR y CALIDAD, mostrar panel autenticado con aviso de Sprint 2
        $infoModuloSprint2 = match ($rolNombre) {
            'ADMINISTRADOR' => [
                'titulo' => 'Panel de Control del Administrador',
                'badge' => 'Control Institucional Maestro',
                'color' => 'indigo',
                'descripcion' => 'Perfil administrativo autenticado correctamente en el sistema institucional.',
                'modulos_sprint_2' => [
                    ['nombre' => 'Req 9: Dashboard Analítico e Inteligencia de Datos', 'desc' => 'Gráficas de consolidación global, mapas de calor por comuna y tiempos de respuesta.', 'icono' => 'chart'],
                    ['nombre' => 'Gestión Avanzada de Usuarios y Sedes', 'desc' => 'Creación, actualización y auditoría de operadores de ventanilla y encuestadores de campo.', 'icono' => 'users'],
                    ['nombre' => 'Configuración de Parámetros Institucionales', 'desc' => 'Ajuste de tiempos de política (15 días), rangos de alerta y sedes zonales.', 'icono' => 'cog'],
                ],
            ],
            'ENCUESTADOR' => [
                'titulo' => 'Portal de Operaciones de Campo (Encuestador)',
                'badge' => 'Personal Operativo de Campo',
                'color' => 'emerald',
                'descripcion' => 'Personal encuestador autenticado y habilitado para recepción de asignaciones domiciliarias.',
                'modulos_sprint_2' => [
                    ['nombre' => 'Req 6: Hojas de Ruta de 20 Encuestas', 'desc' => 'Agrupación y optimización territorial por cercanía geográfica en comunas/corregimientos.', 'icono' => 'map'],
                    ['nombre' => 'Req 7: Entrega de Rutas en Ventanilla', 'desc' => 'Acta de entrega en ventanilla, firma de recibido y descarga del paquete asignado.', 'icono' => 'clipboard'],
                    ['nombre' => 'Formulario Digital de Visita Domiciliaria', 'desc' => 'Captura offline/online de datos socioeconómicos y confirmación GPS del predio.', 'icono' => 'device'],
                ],
            ],
            'CALIDAD' => [
                'titulo' => 'Módulo de Auditoría y Control de Calidad',
                'badge' => 'Supervisión Metodológica',
                'color' => 'purple',
                'descripcion' => 'Auditoría metodológica y validación de consistencia técnica de encuestas radicadas.',
                'modulos_sprint_2' => [
                    ['nombre' => 'Req 8: Generación de Reportes Técnicos en PDF', 'desc' => 'Descarga de informes consolidados de control de calidad y certificados de visita.', 'icono' => 'document'],
                    ['nombre' => 'Auditoría Cruzada de Cobertura Geográfica', 'desc' => 'Doble verificación de coordenadas versus límites oficiales de comunas y corregimientos.', 'icono' => 'check-circle'],
                    ['nombre' => 'Métricas de Cumplimiento de Política Institucional', 'desc' => 'Alertas tempranas de solicitudes que superen los 15 días calendario de vencimiento.', 'icono' => 'bell'],
                ],
            ],
            default => [
                'titulo' => 'Panel Institucional',
                'badge' => 'Usuario Autenticado',
                'color' => 'slate',
                'descripcion' => 'Sesión activa en el Sistema de Gestión de Encuestas.',
                'modulos_sprint_2' => [],
            ],
        };

        // Métricas informativas de la Entrega 1 para visualización
        $stats = [
            'total_sedes' => PuntoAtencion::where('activo', 1)->count(),
            'total_solicitudes_hoy' => SolicitudEncuesta::whereDate('fecha_radicacion', Carbon::today())->count(),
            'total_historico' => SolicitudEncuesta::count(),
            'total_usuarios' => User::count(),
        ];

        return view('dashboard.placeholder', compact('usuario', 'rolNombre', 'infoModuloSprint2', 'stats'));
    }
}
