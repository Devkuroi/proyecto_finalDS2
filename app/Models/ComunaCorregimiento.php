<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComunaCorregimiento extends Model
{
    protected $table = 'comunas_corregimientos';

    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'numero',
        'nombre',
        'zona',
        'created_at',
    ];

    public function barriosVeredas(): HasMany
    {
        return $this->hasMany(BarrioVereda::class, 'comuna_corregimiento_id');
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(SolicitudEncuesta::class, 'comuna_corregimiento_id');
    }
}
