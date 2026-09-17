<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorAlumno extends Model
{
    use HasFactory;

    protected $table = 'tutor_alumnos';

    protected $fillable = [
        'usuario_idUsuario',
        'alumno_idAlumno',
        'parentesco',
        'esResponsable',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'esResponsable' => 'boolean',
            'estado' => 'boolean',
        ];
    }

    public function tutor()
    {
        return $this->belongsTo(Usuario::class, 'usuario_idUsuario', 'idUsuario');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_idAlumno', 'idAlumno');
    }
}