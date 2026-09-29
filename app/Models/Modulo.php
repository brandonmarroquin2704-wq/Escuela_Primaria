<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $primaryKey = 'idModulo';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
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
            'modulo_idModulo',
            'idModulo'
        );
    }
}