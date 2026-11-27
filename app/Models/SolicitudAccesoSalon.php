<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudAccesoSalon extends Model
{
    use HasFactory;

    // Máximo de veces que un tutor puede enviar la solicitud para un mismo alumno.
    public const MAX_INTENTOS = 3;

    protected $table = 'solicitudes_acceso_salon';

    protected $primaryKey = 'idSolicitudAccesoSalon';

    protected $fillable = [
        'tutor_idUsuario',
        'alumno_idAlumno',
        'docente_validador_idUsuario',
        'parentesco',
        'esResponsable',
        'estadoSolicitud',
        'intentos',
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
            'intentos' => 'integer',
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