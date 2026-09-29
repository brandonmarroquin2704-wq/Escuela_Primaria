<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosTutor extends Model
{
    use HasFactory;

    protected $table = 'datos_tutores';

    protected $primaryKey = 'idDatosTutor';

    protected $fillable = [
        'usuario_idUsuario',
        'tipoResponsable',
        'telefono',
        'nombreReferencia',
        'telefonoReferencia',
        'relacionReferencia',
        'fRegistro',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fRegistro' => 'datetime',
            'estado' => 'boolean',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_idUsuario',
            'idUsuario'
        );
    }
}