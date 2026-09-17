<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Control general del sistema',
            ],
            [
                'nombre' => 'Director',
                'descripcion' => 'Supervisión académica y administrativa',
            ],
            [
                'nombre' => 'Docente',
                'descripcion' => 'Registro de asistencia y calificaciones',
            ],
            [
                'nombre' => 'Tutor',
                'descripcion' => 'Consulta de información de sus hijos',
            ],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['nombre' => $rol['nombre']],
                ['descripcion' => $rol['descripcion']]
            );
        }
    }
}