<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';

    protected $primaryKey = 'idAlumno';

    protected $fillable = [
        'nombre',
        'apellidoPaterno',
        'apellidoMaterno',
        'fNac',
        'estado',
        'fechaRegistro',
        'grupo_idGrupo',
    ];

    protected function casts(): array
    {
        return [
            'fNac' => 'date',
            'fechaRegistro' => 'date',
            'estado' => 'boolean',
        ];
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_idGrupo', 'idGrupo');
    }
}