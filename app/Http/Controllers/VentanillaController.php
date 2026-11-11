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
            'latitud' => ['required', 'numeric', 'between:3.20000000,3.53000000'],
            'longitud' => ['required', 'numeric', 'between:-76.65000000,-76.45000000'],
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
            'latitud.between' => 'La ubicación seleccionada está fuera de Santiago de Cali. Solo se permite georreferenciar predios dentro de Cali.',
            'longitud.required' => 'Debe georreferenciar el predio en el mapa interactivo (longitud requerida).',
            'longitud.between' => 'La ubicación seleccionada está fuera de Santiago de Cali. Solo se permite georreferenciar predios dentro de Cali.',
            'punto_atencion_id.required' => 'El punto de atención institucional es obligatorio.',
        ]);

        // Validación estricta de que la dirección corresponda y exista en Cali
        $validacionDireccion = $this->validarDireccionCali($validated['direccion']);
        if (!$validacionDireccion['valida']) {
            return response()->json([
                'success' => false,
                'message' => $validacionDireccion['mensaje'],
                'errors' => [
                    'direccion' => [$validacionDireccion['mensaje']],
                ],
            ], 422);
        }

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

    /**
     * Valida que la dirección física ingresada tenga nomenclatura válida y corresponda a Santiago de Cali
     */
    protected function validarDireccionCali(string $direccion): array
    {
        $dir = trim($direccion);
        if (mb_strlen($dir, 'UTF-8') < 5) {
            return [
                'valida' => false,
                'mensaje' => 'La dirección ingresada es demasiado corta para ser válida en Cali.',
            ];
        }

        $dirLower = mb_strtolower($dir, 'UTF-8');

        // Municipios o ciudades ajenas a Santiago de Cali
        $municipiosAjenos = [
            'bogota', 'bogotá', 'medellin', 'medellín', 'barranquilla', 'bucaramanga',
            'cartagena', 'pereira', 'manizales', 'pasto', 'popayan', 'popayán',
            'yumbo', 'jamundi', 'jamundí', 'palmira', 'candelaria', 'buenaventura',
            'tulua', 'tuluá', 'buga', 'cartago', 'armenia', 'ibague', 'ibagué',
            'cúcuta', 'cucuta', 'valledupar', 'villavicencio', 'santa marta'
        ];

        foreach ($municipiosAjenos as $mun) {
            if (preg_match('/\b' . preg_quote($mun, '/') . '\b/u', $dirLower)) {
                return [
                    'valida' => false,
                    'mensaje' => 'La dirección ingresada hace referencia a otro municipio (' . ucfirst($mun) . ') fuera de Santiago de Cali.',
                ];
            }
        }

        // Patrón de nomenclatura urbana de Cali
        // Ej: Calle 13B # 85-20, Cra 100 # 15-30, Av 6 Norte, Diagonal 23, Transversal 5, Mz 4
        $patronUrbano = '/\b(calle|carrera|cra|cll|cl|cr|avenida|av|diagonal|diag|dg|transversal|trans|tv|autopista|circunvalar|callejon|callejón|pasaje|manzana|mz)\b\.?\s+[0-9a-z#\-\s\.]+/iu';

        // Patrón de nomenclatura rural de Cali (Corregimientos: Pance, La Buitrera, Montebello, Felidia, etc.)
        // Ej: Km 12 Vía Vorágine, Vereda San Francisco, Corregimiento Navarro, Sector La Fonda, Finca El Paraíso
        $patronRural = '/\b(km|kilometro|kilómetro|vereda|corregimiento|sector|finca|lote|parcela|parcelacion|parcelación|hacienda|hda|callejon|callejón|via|vía)\b\.?\s+[0-9a-z#\-\s\.]+/iu';

        if (!preg_match($patronUrbano, $dir) && !preg_match($patronRural, $dir)) {
            return [
                'valida' => false,
                'mensaje' => 'La dirección no cuenta con una nomenclatura válida para Santiago de Cali (debe iniciar o contener Calle, Carrera, Avenida, Diagonal, Transversal, Km, Vereda o Corregimiento con su numeración o referencia).',
            ];
        }

        return [
            'valida' => true,
            'mensaje' => 'Dirección válida en Cali.',
        ];
    }
}
