<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $primaryKey = 'idPermiso';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function rolPermisos()
    {
        return $this->hasMany(
            RolPermiso::class,
            'permiso_idPermiso',
            'idPermiso'
        );
    }
}