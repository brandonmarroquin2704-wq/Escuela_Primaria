<?php

namespace Database\Seeders;

use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\RolPermiso;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = Rol::where('nombre', 'Administrador')->firstOrFail();
        $director = Rol::where('nombre', 'Director')->firstOrFail();

        $permisos = Permiso::pluck('idPermiso', 'nombre');
        $modulos = Modulo::pluck('idModulo', 'nombre');

        $matriz = [
            'Administrador' => [
                'Perfil' => ['Consultar', 'Editar'],
                'Escuelas' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar'],
                'Periodos escolares' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar'],
                'Roles' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar'],
                'Usuarios' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar', 'Asignar'],
                'Asignaturas' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar', 'Eliminar'],
                'Grupos' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar'],
                'Alumnos' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar'],
                'Tutores y alumnos' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar', 'Asignar'],
                'Asignación de materias' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar', 'Asignar'],
                'Asistencia' => ['Consultar'],
                'Calificaciones' => ['Consultar', 'Desbloquear'],
                'Correcciones de calificaciones' => ['Consultar', 'Desbloquear'],
                'Avisos' => ['Consultar', 'Registrar', 'Editar', 'Publicar', 'Moderar'],
                'Comentarios' => ['Consultar', 'Moderar'],
                'Permisos por rol' => ['Consultar', 'Registrar', 'Editar', 'Activar/Inactivar'],
            ],

            'Director' => [
                'Perfil' => ['Consultar', 'Editar'],
                'Escuelas' => ['Consultar'],
                'Periodos escolares' => ['Consultar'],
                'Usuarios' => ['Consultar'],
                'Asignaturas' => ['Consultar'],
                'Grupos' => ['Consultar'],
                'Alumnos' => ['Consultar'],
                'Tutores y alumnos' => ['Consultar'],
                'Asignación de materias' => ['Consultar'],
                'Calificaciones' => ['Consultar'],
                'Correcciones de calificaciones' => ['Consultar'],
                'Avisos' => ['Consultar', 'Registrar', 'Editar', 'Publicar'],
                'Comentarios' => ['Consultar', 'Moderar'],
            ],
        ];

        $roles = [
            'Administrador' => $administrador,
            'Director' => $director,
        ];

        foreach ($matriz as $nombreRol => $permisosPorModulo) {
            foreach ($permisosPorModulo as $nombreModulo => $acciones) {
                foreach ($acciones as $nombrePermiso) {
                    RolPermiso::updateOrCreate(
                        [
                            'rol_idRol' => $roles[$nombreRol]->idRol,
                            'modulo_idModulo' => $modulos[$nombreModulo],
                            'permiso_idPermiso' => $permisos[$nombrePermiso],
                        ],
                        [
                            'estado' => true,
                        ]
                    );
                }
            }
        }
    }
}