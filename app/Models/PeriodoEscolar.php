<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoEscolar extends Model
{
    use HasFactory;

    protected $table = 'periodos_escolares';

    protected $primaryKey = 'idPeriodo';

    protected $fillable = [
        'fechaInicio',
        'fechaFin',
        'estado',
        'cicloEscolar',
        'nombre',
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date',
        'estado' => 'boolean',
    ];

    /**
     * Un periodo escolar puede tener muchas calificaciones.
     * Esta relación funcionará al crear Calificacion.php.
     */
    public function calificaciones()
    {
        return $this->hasMany(
            Calificacion::class,
            'periodo_escolar_idPeriodo',
            'idPeriodo'
        );
    }
}