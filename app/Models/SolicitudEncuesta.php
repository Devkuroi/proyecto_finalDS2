<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SolicitudEncuesta extends Model
{
    protected $table = 'solicitudes_encuesta';

    protected $fillable = [
        'radicado_codigo',
        'nombres_solicitante',
        'apellidos_solicitante',
        'celular',
        'direccion',
        'barrio_id',
        'comuna_corregimiento_id',
        'latitud',
        'longitud',
        'punto_atencion_id',
        'usuario_ventanilla_id',
        'fecha_radicacion',
        'fecha_limite',
        'estado',
        'observaciones',
    ];

    protected $appends = [
        'nombre_completo',
        'dias_restantes',
        'esta_vencida',
    ];

    protected function casts(): array
    {
        return [
            'fecha_radicacion' => 'datetime',
            'fecha_limite' => 'date',
            'latitud' => 'float',
            'longitud' => 'float',
        ];
    }

    /**
     * Boot del modelo para autocalcular fecha límite y radicado si no están presentes
     */
    protected static function booted(): void
    {
        static::creating(function (SolicitudEncuesta $solicitud) {
            if (empty($solicitud->fecha_radicacion)) {
                $solicitud->fecha_radicacion = now();
            }

            // Política institucional estricta: +15 días calendario a partir de la radicación
            if (empty($solicitud->fecha_limite)) {
                $radicacion = Carbon::parse($solicitud->fecha_radicacion);
                $solicitud->fecha_limite = $radicacion->copy()->addDays(15)->toDateString();
            }

            if (empty($solicitud->estado)) {
                $solicitud->estado = 'RADICADA';
            }

            if (empty($solicitud->radicado_codigo)) {
                $fechaStr = Carbon::parse($solicitud->fecha_radicacion)->format('Ymd');
                $countHoy = static::whereDate('fecha_radicacion', Carbon::parse($solicitud->fecha_radicacion)->toDateString())->count() + 1;
                $solicitud->radicado_codigo = sprintf('SOL-%s-%04d', $fechaStr, $countHoy);
            }
        });
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(BarrioVereda::class, 'barrio_id');
    }

    public function comunaCorregimiento(): BelongsTo
    {
        return $this->belongsTo(ComunaCorregimiento::class, 'comuna_corregimiento_id');
    }

    public function puntoAtencion(): BelongsTo
    {
        return $this->belongsTo(PuntoAtencion::class, 'punto_atencion_id');
    }

    public function usuarioVentanilla(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_ventanilla_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres_solicitante} {$this->apellidos_solicitante}");
    }

    /**
     * Días restantes para el vencimiento de la política institucional de 15 días
     */
    public function getDiasRestantesAttribute(): int
    {
        $hoy = Carbon::today();
        $limite = Carbon::parse($this->fecha_limite)->startOfDay();
        return (int) $hoy->diffInDays($limite, false);
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->dias_restantes < 0;
    }
}
