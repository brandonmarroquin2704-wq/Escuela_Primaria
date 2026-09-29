<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudAccesoSalon extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_acceso_salon';

    protected $primaryKey = 'idSolicitudAccesoSalon';

    protected $fillable = [
        'tutor_idUsuario',
        'alumno_idAlumno',
        'docente_validador_idUsuario',
        'parentesco',
        'esResponsable',
        'estadoSolicitud',
        'fechaSolicitud',
        'fechaRespuesta',
        'observacionRespuesta',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'esResponsable' => 'boolean',
            'estado' => 'boolean',
            'fechaSolicitud' => 'datetime',
            'fechaRespuesta' => 'datetime',
        ];
    }

    public function tutor()
    {
        return $this->belongsTo(
            Usuario::class,
            'tutor_idUsuario',
            'idUsuario'
        );
    }

    public function alumno()
    {
        return $this->belongsTo(
            Alumno::class,
            'alumno_idAlumno',
            'idAlumno'
        );
    }

    public function docenteValidador()
    {
        return $this->belongsTo(
            Usuario::class,
            'docente_validador_idUsuario',
            'idUsuario'
        );
    }
}