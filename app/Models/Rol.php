<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'idRol';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $casts = [
    'created_at' => 'datetime',
];

    public function rolPermisos()
    {
        return $this->hasMany(
            RolPermiso::class,
            'rol_idRol',
            'idRol'
        );
    }
}