<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComunaCorregimiento;
use App\Models\BarrioVereda;
use App\Models\PuntoAtencion;

class TerritorioController extends Controller
{
    /**
     * Retorna el listado completo de Comunas (1-22) y Corregimientos de Cali
     */
    public function getComunasCorregimientos(Request $request)
    {
        $query = ComunaCorregimiento::query();

        if ($request->filled('tipo')) {
            $query->where('tipo', strtoupper($request->tipo));
        }

        if ($request->filled('zona')) {
            $query->where('zona', strtoupper($request->zona));
        }

        $divisiones = $query->orderBy('tipo')
            ->orderByRaw('CASE WHEN numero IS NULL THEN 999 ELSE numero END ASC')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $divisiones->count(),
            'data' => $divisiones,
        ]);
    }

    /**
     * Retorna los barrios o veredas asociados estrictamente a una comuna o corregimiento
     * Previene el error humano de asignación errónea
     */
    public function getBarrios(Request $request)
    {
        $comunaId = $request->query('comuna_id') ?? $request->query('comuna_corregimiento_id');

        if (!$comunaId) {
            return response()->json([
                'success' => false,
                'message' => 'El parámetro comuna_id es requerido.',
                'data' => [],
            ], 400);
        }

        $barrios = BarrioVereda::where('comuna_corregimiento_id', $comunaId)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'comuna_corregimiento_id' => (int) $comunaId,
            'total' => $barrios->count(),
            'data' => $barrios,
        ]);
    }

    /**
     * Retorna los puntos de atención oficiales en Cali con coordenadas para Leaflet
     */
    public function getPuntosAtencion()
    {
        $puntos = PuntoAtencion::where('activo', 1)
            ->orderByDesc('es_principal')
            ->orderBy('comuna_numero')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $puntos->count(),
            'data' => $puntos,
        ]);
    }
}
