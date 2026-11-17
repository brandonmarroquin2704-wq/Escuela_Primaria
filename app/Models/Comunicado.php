<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comunicado extends Model
{
    use SoftDeletes;

    protected $table = 'comunicados';

    public const TIPOS = [
        'anuncio' => 'Anuncio',
        'tarea' => 'Tarea',
        'aviso' => 'Aviso',
    ];

    protected $fillable = [
        'asignacion_id',
        'docente_idUsuario',
        'tipo',
        'titulo',
        'contenido',
        'fecha_entrega',
    ];

    protected function casts(): array
    {
        return ['fecha_entrega' => 'date'];
    }

    public function asignacion()
    {
        return $this->belongsTo(AsignacionDocenteAsignatura::class, 'asignacion_id');
    }

    public function docente()
    {
        return $this->belongsTo(Usuario::class, 'docente_idUsuario', 'idUsuario');
    }

    public function mensajeAutomatico(): string
    {
        $docente = trim(($this->docente?->nombre ?? '') . ' ' . ($this->docente?->apellidoPaterno ?? ''));
        $materia = $this->asignacion?->asignatura?->nombre;

        return 'Este espacio es solo informativo y no permite respuestas. '
            . 'Para cualquier duda o comentario sobre este comunicado, '
            . 'comuníquese directamente con '
            . ($docente !== '' ? 'el docente ' . $docente : 'el docente correspondiente')
            . ($materia ? ' (' . $materia . ')' : '')
            . '.';
    }
}