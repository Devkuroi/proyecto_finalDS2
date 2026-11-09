<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuntoAtencion extends Model
{
    protected $table = 'puntos_atencion';

    protected $fillable = [
        'nombre',
        'codigo',
        'comuna_numero',
        'direccion',
        'telefono',
        'latitud',
        'longitud',
        'es_principal',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'es_principal' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'punto_atencion_id');
    }

    public function solicitudesRadicadas(): HasMany
    {
        return $this->hasMany(SolicitudEncuesta::class, 'punto_atencion_id');
    }
}
