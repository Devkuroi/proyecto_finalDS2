<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudEncuesta;
use App\Models\PuntoAtencion;
use App\Models\ComunaCorregimiento;
use Carbon\Carbon;

class EnrutadorController extends Controller
{
    /**
     * Muestra la vista de Bitácora Diaria del Enrutador con mapa consolidado (Req 4)
     */
    public function index(Request $request)
    {
        $usuario = Auth::user()->load(['rol', 'puntoAtencion']);
        $puntosAtencion = PuntoAtencion::where('activo', 1)->orderBy('comuna_numero')->get();
        $comunas = ComunaCorregimiento::orderBy('nombre')->get();
        $fechaDefecto = Carbon::today()->toDateString();

        return view('enrutador.bitacora', compact('usuario', 'puntosAtencion', 'comunas', 'fechaDefecto'));
    }

    /**
     * Endpoint API GET /api/enrutador/bitacora-diaria
     * Consulta todas las solicitudes generadas hoy o por fecha consolidando todas las sedes (Req 4)
     */
    public function bitacoraDiaria(Request $request)
    {
        $fechaConsulta = $request->query('fecha', Carbon::today()->toDateString());
        $puntoAtencionId = $request->query('punto_atencion_id');
        $comunaId = $request->query('comuna_id');
        $zona = $request->query('zona');

        $query = SolicitudEncuesta::with([
            'barrio:id,comuna_corregimiento_id,tipo,nombre,codigo_dane',
            'comunaCorregimiento:id,tipo,numero,nombre,zona',
            'puntoAtencion:id,nombre,codigo,comuna_numero,direccion,latitud,longitud,es_principal',
            'usuarioVentanilla:id,nombre,email,identificacion'
        ])
        ->whereDate('fecha_radicacion', $fechaConsulta);

        if ($puntoAtencionId) {
            $query->where('punto_atencion_id', $puntoAtencionId);
        }

        if ($comunaId) {
            $query->where('comuna_corregimiento_id', $comunaId);
        }

        if ($zona) {
            $query->whereHas('comunaCorregimiento', function ($q) use ($zona) {
                $q->where('zona', strtoupper($zona));
            });
        }

        $solicitudes = $query->orderBy('fecha_radicacion', 'asc')->get();

        // Transformar la colección para incluir datos explícitos requeridos por la bitácora
        $solicitudesFormateadas = $solicitudes->map(function ($s) {
            return [
                'id' => $s->id,
                'radicado_codigo' => $s->radicado_codigo,
                'solicitante' => $s->nombre_completo,
                'nombres' => $s->nombres_solicitante,
                'apellidos' => $s->apellidos_solicitante,
                'celular' => $s->celular,
                'direccion' => $s->direccion,
                'comuna_corregimiento_id' => $s->comuna_corregimiento_id,
                'comuna_nombre' => $s->comunaCorregimiento?->nombre ?? 'N/D',
                'comuna_numero' => $s->comunaCorregimiento?->numero,
                'zona' => $s->comunaCorregimiento?->zona ?? 'URBANA',
                'barrio_id' => $s->barrio_id,
                'barrio_nombre' => $s->barrio?->nombre ?? 'N/D',
                'latitud' => (float) $s->latitud,
                'longitud' => (float) $s->longitud,
                'punto_atencion_id' => $s->punto_atencion_id,
                'punto_atencion_nombre' => $s->puntoAtencion?->nombre ?? 'N/D',
                'punto_atencion_comuna' => $s->puntoAtencion?->comuna_numero,
                'usuario_ventanilla' => $s->usuarioVentanilla?->nombre ?? 'N/D',
                'fecha_radicacion' => $s->fecha_radicacion->format('Y-m-d H:i:s'),
                'hora_radicacion' => $s->fecha_radicacion->format('h:i A'),
                'fecha_limite' => $s->fecha_limite->format('Y-m-d'),
                'dias_restantes' => $s->dias_restantes,
                'esta_vencida' => $s->esta_vencida,
                'estado' => $s->estado,
                'observaciones' => $s->observaciones,
            ];
        });

        // Métricas consolidadas del día
        $totalDia = $solicitudesFormateadas->count();
        $urbanas = $solicitudesFormateadas->where('zona', 'URBANA')->count();
        $rurales = $solicitudesFormateadas->where('zona', 'RURAL')->count();

        // Agrupación por Punto de Atención (Comunas 1, 6, 7, 13, 14, 17, 20 y 21)
        $porPuntoAtencion = PuntoAtencion::where('activo', 1)
            ->get()
            ->map(function ($pto) use ($solicitudesFormateadas) {
                $count = $solicitudesFormateadas->where('punto_atencion_id', $pto->id)->count();
                return [
                    'id' => $pto->id,
                    'nombre' => $pto->nombre,
                    'comuna_numero' => $pto->comuna_numero,
                    'es_principal' => $pto->es_principal,
                    'total_radicadas' => $count,
                ];
            });

        return response()->json([
            'success' => true,
            'fecha_consulta' => $fechaConsulta,
            'total_solicitudes' => $totalDia,
            'metricas' => [
                'total' => $totalDia,
                'urbanas' => $urbanas,
                'rurales' => $rurales,
                'vigentes' => $solicitudesFormateadas->where('esta_vencida', false)->count(),
                'vencidas' => $solicitudesFormateadas->where('esta_vencida', true)->count(),
            ],
            'consolidado_puntos_atencion' => $porPuntoAtencion,
            'data' => $solicitudesFormateadas,
        ]);
    }
}
