<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    protected $primaryKey = 'idGrupo';

    protected $fillable = [
        'grado',
        'letraGrupo',
        'cicloEsc',
        'estado',
        'idMaestro',
        'escuela_idEscuela',
    ];

    protected function casts(): array
    {
        return [
            'grado' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function maestro()
    {
        return $this->belongsTo(Usuario::class, 'idMaestro', 'idUsuario');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'escuela_idEscuela', 'idEscuela');
    }
}