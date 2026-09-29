<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    use HasFactory;

    protected $table = 'rol_permisos';

    protected $primaryKey = 'idRolPermiso';

    public $timestamps = false;

    protected $fillable = [
        'rol_idRol',
        'modulo_idModulo',
        'permiso_idPermiso',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(
            Rol::class,
            'rol_idRol',
            'idRol'
        );
    }

    public function modulo()
    {
        return $this->belongsTo(
            Modulo::class,
            'modulo_idModulo',
            'idModulo'
        );
    }

    public function permiso()
    {
        return $this->belongsTo(
            Permiso::class,
            'permiso_idPermiso',
            'idPermiso'
        );
    }
}