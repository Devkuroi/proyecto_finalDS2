<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol_id',
        'punto_atencion_id',
        'identificacion',
        'telefono',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function puntoAtencion(): BelongsTo
    {
        return $this->belongsTo(PuntoAtencion::class, 'punto_atencion_id');
    }

    public function solicitudesRadicadas(): HasMany
    {
        return $this->hasMany(SolicitudEncuesta::class, 'usuario_ventanilla_id');
    }

    public function hasRole(string $nombreRol): bool
    {
        return $this->rol && strtoupper($this->rol->nombre) === strtoupper($nombreRol);
    }

    public function esAdministrador(): bool
    {
        return $this->hasRole('ADMINISTRADOR');
    }

    public function esVentanilla(): bool
    {
        return $this->hasRole('VENTANILLA');
    }

    public function esEnrutador(): bool
    {
        return $this->hasRole('ENRUTADOR');
    }

    public function esEncuestador(): bool
    {
        return $this->hasRole('ENCUESTADOR');
    }

    public function esCalidad(): bool
    {
        return $this->hasRole('CALIDAD');
    }
}
