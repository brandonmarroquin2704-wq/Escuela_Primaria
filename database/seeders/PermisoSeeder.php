<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            [
                'nombre' => 'Consultar',
                'descripcion' => 'Permite visualizar información y listados',
                'estado' => true,
            ],
            [
                'nombre' => 'Registrar',
                'descripcion' => 'Permite crear nuevos registros',
                'estado' => true,
            ],
            [
                'nombre' => 'Editar',
                'descripcion' => 'Permite modificar registros existentes',
                'estado' => true,
            ],
            [
                'nombre' => 'Eliminar',
                'descripcion' => 'Permite eliminar registros cuando aplique',
                'estado' => true,
            ],
            [
                'nombre' => 'Activar/Inactivar',
                'descripcion' => 'Permite cambiar el estado sin eliminar registros',
                'estado' => true,
            ],
            [
                'nombre' => 'Asignar',
                'descripcion' => 'Permite crear relaciones entre registros escolares',
                'estado' => true,
            ],
            [
                'nombre' => 'Publicar',
                'descripcion' => 'Permite publicar avisos institucionales o académicos',
                'estado' => true,
            ],
            [
                'nombre' => 'Moderar',
                'descripcion' => 'Permite moderar avisos y comentarios de otros usuarios',
                'estado' => true,
            ],
            [
                'nombre' => 'Desbloquear',
                'descripcion' => 'Permite autorizar correcciones excepcionales de calificaciones',
                'estado' => true,
            ],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['nombre' => $permiso['nombre']],
                [
                    'descripcion' => $permiso['descripcion'],
                    'estado' => $permiso['estado'],
                ]
            );
        }
    }
}