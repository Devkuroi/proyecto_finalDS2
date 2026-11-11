<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudEncuesta;
use App\Models\ComunaCorregimiento;
use App\Models\BarrioVereda;
use App\Models\PuntoAtencion;
use Carbon\Carbon;

class VentanillaController extends Controller
{
    /**
     * Muestra la vista de radicación en ventanilla con formulario interactivo y Leaflet GIS
     */
    public function index()
    {
        $usuario = Auth::user()->load(['rol', 'puntoAtencion']);

        $comunas = ComunaCorregimiento::orderBy('tipo')
            ->orderByRaw('CASE WHEN numero IS NULL THEN 999 ELSE numero END ASC')
            ->orderBy('nombre')
            ->get();

        $puntosAtencion = PuntoAtencion::where('activo', 1)
            ->orderByDesc('es_principal')
            ->orderBy('comuna_numero')
            ->get();

        // Punto de atención asignado al operador de ventanilla por defecto
        $puntoAtencionPredeterminado = $usuario->puntoAtencion ?? $puntosAtencion->first();

        // Solicitudes radicadas hoy por este usuario de ventanilla
        $solicitudesHoy = SolicitudEncuesta::with(['barrio', 'comunaCorregimiento', 'puntoAtencion'])
            ->where('usuario_ventanilla_id', $usuario->id)
            ->whereDate('fecha_radicacion', Carbon::today())
            ->orderByDesc('fecha_radicacion')
            ->take(10)
            ->get();

        return view('ventanilla.index', compact('usuario', 'comunas', 'puntosAtencion', 'puntoAtencionPredeterminado', 'solicitudesHoy'));
    }

    /**
     * Endpoint API para registrar una nueva solicitud de encuesta domiciliaria (Req 2 y 3)
     */
    public function storeSolicitud(Request $request)
    {
        $validated = $request->validate([
            'nombres_solicitante' => ['required', 'string', 'min:2', 'max:100'],
            'apellidos_solicitante' => ['required', 'string', 'min:2', 'max:100'],
            'celular' => ['required', 'string', 'regex:/^[0-9]{7,15}$/'],
            'direccion' => ['required', 'string', 'min:5', 'max:255'],
            'comuna_corregimiento_id' => ['required', 'integer', 'exists:comunas_corregimientos,id'],
            'barrio_id' => ['required', 'integer', 'exists:barrios_veredas,id'],
            'latitud' => ['required', 'numeric', 'between:3.20000000,3.60000000'],
            'longitud' => ['required', 'numeric', 'between:-76.68000000,-76.40000000'],
            'punto_atencion_id' => ['required', 'integer', 'exists:puntos_atencion,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'nombres_solicitante.required' => 'El nombre del solicitante es obligatorio.',
            'apellidos_solicitante.required' => 'Los apellidos del solicitante son obligatorios.',
            'celular.required' => 'El número de celular de contacto es obligatorio.',
            'celular.regex' => 'Ingrese un número de celular válido (entre 7 y 15 dígitos numéricos).',
            'direccion.required' => 'La dirección física del predio es obligatoria.',
            'comuna_corregimiento_id.required' => 'Debe seleccionar una comuna o corregimiento de Cali.',
            'barrio_id.required' => 'Debe seleccionar un barrio o vereda.',
            'latitud.required' => 'Debe georreferenciar el predio en el mapa interactivo (latitud requerida).',
            'latitud.between' => 'La coordenada de latitud debe corresponder al perímetro del municipio de Santiago de Cali.',
            'longitud.required' => 'Debe georreferenciar el predio en el mapa interactivo (longitud requerida).',
            'longitud.between' => 'La coordenada de longitud debe corresponder al perímetro del municipio de Santiago de Cali.',
            'punto_atencion_id.required' => 'El punto de atención institucional es obligatorio.',
        ]);

        // Validación estricta de coherencia territorial:
        // El barrio seleccionado DEBE pertenecer a la comuna/corregimiento elegida
        $barrioValido = BarrioVereda::where('id', $validated['barrio_id'])
            ->where('comuna_corregimiento_id', $validated['comuna_corregimiento_id'])
            ->exists();

        if (!$barrioValido) {
            return response()->json([
                'success' => false,
                'message' => 'Incoherencia territorial: El barrio o vereda seleccionado no pertenece a la comuna o corregimiento indicada.',
                'errors' => [
                    'barrio_id' => ['El barrio no corresponde a la comuna/corregimiento seleccionada.'],
                ],
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ahora = Carbon::now();
            $fechaLimite = $ahora->copy()->addDays(15)->toDateString(); // Política institucional: +15 días calendario
            $fechaCodigo = $ahora->format('Ymd');

            // Generar código único correlativo por día
            $consecutivo = SolicitudEncuesta::whereDate('fecha_radicacion', $ahora->toDateString())->count() + 1;
            $radicadoCodigo = sprintf('SOL-%s-%04d', $fechaCodigo, $consecutivo);

            // Asegurar unicidad si existe colisión
            while (SolicitudEncuesta::where('radicado_codigo', $radicadoCodigo)->exists()) {
                $consecutivo++;
                $radicadoCodigo = sprintf('SOL-%s-%04d', $fechaCodigo, $consecutivo);
            }

            $solicitud = new SolicitudEncuesta();
            $solicitud->radicado_codigo = $radicadoCodigo;
            $solicitud->nombres_solicitante = trim($validated['nombres_solicitante']);
            $solicitud->apellidos_solicitante = trim($validated['apellidos_solicitante']);
            $solicitud->celular = trim($validated['celular']);
            $solicitud->direccion = trim($validated['direccion']);
            $solicitud->comuna_corregimiento_id = $validated['comuna_corregimiento_id'];
            $solicitud->barrio_id = $validated['barrio_id'];
            $solicitud->latitud = $validated['latitud'];
            $solicitud->longitud = $validated['longitud'];
            $solicitud->punto_atencion_id = $validated['punto_atencion_id'];
            $solicitud->usuario_ventanilla_id = Auth::id() ?? 2; // Operador de ventanilla
            $solicitud->fecha_radicacion = $ahora;
            $solicitud->fecha_limite = $fechaLimite;
            $solicitud->estado = 'RADICADA';
            $solicitud->observaciones = $validated['observaciones'] ?? null;
            $solicitud->save();

            DB::commit();

            $solicitud->load(['barrio', 'comunaCorregimiento', 'puntoAtencion', 'usuarioVentanilla']);

            return response()->json([
                'success' => true,
                'message' => "Solicitud radicada exitosamente bajo el código {$solicitud->radicado_codigo}.",
                'data' => [
                    'id' => $solicitud->id,
                    'radicado_codigo' => $solicitud->radicado_codigo,
                    'solicitante' => $solicitud->nombre_completo,
                    'celular' => $solicitud->celular,
                    'direccion' => $solicitud->direccion,
                    'barrio' => $solicitud->barrio?->nombre,
                    'comuna_corregimiento' => $solicitud->comunaCorregimiento?->nombre,
                    'latitud' => $solicitud->latitud,
                    'longitud' => $solicitud->longitud,
                    'punto_atencion' => $solicitud->puntoAtencion?->nombre,
                    'fecha_radicacion' => $solicitud->fecha_radicacion->format('Y-m-d H:i:s'),
                    'fecha_limite' => $solicitud->fecha_limite->format('Y-m-d'),
                    'dias_restantes' => $solicitud->dias_restantes,
                    'estado' => $solicitud->estado,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al radicar la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna el listado de solicitudes recientes para actualizar la tabla asíncronamente
     */
    public function solicitudesRecientes(Request $request)
    {
        $userId = Auth::id();
        $query = SolicitudEncuesta::with(['barrio', 'comunaCorregimiento', 'puntoAtencion'])
            ->orderByDesc('fecha_radicacion');

        if ($userId) {
            $query->where('usuario_ventanilla_id', $userId);
        }

        $solicitudes = $query->take(15)->get();

        return response()->json([
            'success' => true,
            'total' => $solicitudes->count(),
            'data' => $solicitudes,
        ]);
    }
}
