<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarrioVereda extends Model
{
    protected $table = 'barrios_veredas';

    public $timestamps = false;

    protected $fillable = [
        'comuna_corregimiento_id',
        'tipo',
        'nombre',
        'codigo_dane',
        'latitud_referencia',
        'longitud_referencia',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'latitud_referencia' => 'float',
            'longitud_referencia' => 'float',
        ];
    }

    public function comunaCorregimiento(): BelongsTo
    {
        return $this->belongsTo(ComunaCorregimiento::class, 'comuna_corregimiento_id');
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(SolicitudEncuesta::class, 'barrio_id');
    }
}
