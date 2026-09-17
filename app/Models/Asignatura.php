<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    protected $table = 'asignaturas';

    protected $primaryKey = 'idAsignatura';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];

    /**
     * Una asignatura puede estar en muchas calificaciones.
     * Funcionará al crear Calificacion.php.
     */
    public function calificaciones()
    {
        return $this->hasMany(
            Calificacion::class,
            'asignatura_idAsignatura',
            'idAsignatura'
        );
    }
}