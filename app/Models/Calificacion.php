<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $primaryKey = 'idCalificacion';

    protected $fillable = [
        'valor',
        'observacion',
        'fechaRegistro',
        'fechaLimiteEdicion',
        'estado',
        'alumno_idAlumno',
        'periodo_escolar_idPeriodo',
        'asignatura_idAsignatura',
        'usuario_idUsuario',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'fechaRegistro' => 'datetime',
            'fechaLimiteEdicion' => 'datetime',
            'estado' => 'boolean',
        ];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_idAlumno', 'idAlumno');
    }

    public function periodo()
    {
        return $this->belongsTo(PeriodoEscolar::class, 'periodo_escolar_idPeriodo', 'idPeriodo');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_idAsignatura', 'idAsignatura');
    }

    public function docente()
    {
        return $this->belongsTo(Usuario::class, 'usuario_idUsuario', 'idUsuario');
    }

    public function scopePublicadas($query)
    {
        return $query->where('estado', true)
            ->where('fechaLimiteEdicion', '<=', now());
    }

    public function estaEnEdicion(): bool
    {
        return now()->lt($this->fechaLimiteEdicion);
    }
}