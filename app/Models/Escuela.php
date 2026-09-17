<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela extends Model
{
    use HasFactory;

    // Tabla que este modelo manejará en MySQL.
    protected $table = 'escuelas';

    // Llave primaria personalizada del diagrama.
    protected $primaryKey = 'idEscuela';

    // Campos permitidos al crear o actualizar una escuela.
    protected $fillable = [
        'nombre',
        'claveCentroTrabajo',
        'telefono',
        'correo',
        'direccion',
        'municipio',
        'codigoPostal',
        'estado',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    // Convierte automáticamente estos campos a los tipos correctos.
    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];

    /**
     * Una escuela puede tener muchos grupos.
     * Esta relación funcionará cuando creemos Grupo y su migración.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'escuela_idEscuela', 'idEscuela');
    }
}