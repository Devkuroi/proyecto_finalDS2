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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con el rol del usuario
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación con el punto de atención asignado (si aplica)
     */
    public function puntoAtencion(): BelongsTo
    {
        return $this->belongsTo(PuntoAtencion::class, 'punto_atencion_id');
    }

    /**
     * Solicitudes radicadas por este usuario en ventanilla
     */
    public function solicitudesRadicadas(): HasMany
    {
        return $this->hasMany(SolicitudEncuesta::class, 'usuario_ventanilla_id');
    }

    /**
     * Helper para verificar si tiene un rol específico
     */
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
