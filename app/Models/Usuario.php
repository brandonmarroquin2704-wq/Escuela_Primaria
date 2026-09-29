<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'nombre',
        'apellidoPaterno',
        'apellidoMaterno',
        'correo',
        'contrasena',
        'rol_idRol',
        'fRegistro',
        'estado',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected function casts(): array
    {
        return [
            'fRegistro' => 'date',
            'estado' => 'boolean',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_idRol', 'idRol');
    }

    public function cuentaAcceso()
{
    return $this->hasOne(
        User::class,
        'usuario_idUsuario',
        'idUsuario'
    );
}

    public function datosTutor()
    {
        return $this->hasOne(
            DatosTutor::class,
            'usuario_idUsuario',
            'idUsuario'
        );
    }

    public function solicitudesComoTutor()
    {
        return $this->hasMany(
            SolicitudAccesoSalon::class,
            'tutor_idUsuario',
            'idUsuario'
        );
    }

    public function tienePermiso(string $modulo, string $permiso): bool
{
    if (! $this->rol) {
        return false;
    }

    return \App\Models\RolPermiso::query()
        ->where('rol_idRol', $this->rol->idRol)
        ->where('estado', true)
        ->whereHas('modulo', function ($query) use ($modulo) {
            $query->where('nombre', $modulo)->where('estado', true);
        })
        ->whereHas('permiso', function ($query) use ($permiso) {
            $query->where('nombre', $permiso)->where('estado', true);
        })
        ->exists();
}

    public function solicitudesValidadas()
    {
        return $this->hasMany(
            SolicitudAccesoSalon::class,
            'docente_validador_idUsuario',
            'idUsuario'
        );
    }
}