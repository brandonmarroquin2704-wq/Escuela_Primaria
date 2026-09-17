<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionDocenteAsignatura extends Model
{
    use HasFactory;

    protected $table = 'asignacion_docente_asignaturas';

    protected $fillable = [
        'grupo_idGrupo',
        'asignatura_idAsignatura',
        'docente_idUsuario',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_idGrupo', 'idGrupo');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_idAsignatura', 'idAsignatura');
    }

    public function docente()
    {
        return $this->belongsTo(Usuario::class, 'docente_idUsuario', 'idUsuario');
    }
}