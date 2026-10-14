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
        protected $permisosCache = null;

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

     public function permisos(): \Illuminate\Support\Collection
    {
        if ($this->permisosCache === null) {
            $this->permisosCache = \App\Models\RolPermiso::query()
                ->where('rol_idRol', $this->rol_idRol)
                ->where('estado', true)
                ->whereHas('modulo', fn ($q) => $q->where('estado', true))
                ->whereHas('permiso', fn ($q) => $q->where('estado', true))
                ->with(['modulo:idModulo,nombre', 'permiso:idPermiso,nombre'])
                ->get()
                ->map(fn ($rp) => $rp->modulo->nombre . '.' . $rp->permiso->nombre);
        }

        return $this->permisosCache;
    }

    public function tienePermiso(string $modulo, string $permiso): bool
    {
        return (bool) $this->estado
            && $this->permisos()->contains($modulo . '.' . $permiso);
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