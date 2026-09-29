<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloSeeder extends Seeder
{
    public function run(): void
    {
        $modulos = [
            [
                'nombre' => 'Perfil',
                'descripcion' => 'Consulta y edición del perfil propio',
                'icono' => 'user-circle',
                'estado' => true,
            ],
            [
                'nombre' => 'Escuelas',
                'descripcion' => 'Administración de datos de escuelas',
                'icono' => 'school',
                'estado' => true,
            ],
            [
                'nombre' => 'Periodos escolares',
                'descripcion' => 'Administración de ciclos escolares',
                'icono' => 'calendar',
                'estado' => true,
            ],
            [
                'nombre' => 'Roles',
                'descripcion' => 'Administración de roles del sistema',
                'icono' => 'shield',
                'estado' => true,
            ],
            [
                'nombre' => 'Usuarios',
                'descripcion' => 'Administración de cuentas de usuario',
                'icono' => 'users',
                'estado' => true,
            ],
            [
                'nombre' => 'Asignaturas',
                'descripcion' => 'Administración del catálogo de asignaturas',
                'icono' => 'book-open',
                'estado' => true,
            ],
            [
                'nombre' => 'Grupos',
                'descripcion' => 'Administración de grupos escolares',
                'icono' => 'layers',
                'estado' => true,
            ],
            [
                'nombre' => 'Alumnos',
                'descripcion' => 'Administración de alumnos',
                'icono' => 'graduation-cap',
                'estado' => true,
            ],
            [
                'nombre' => 'Tutores y alumnos',
                'descripcion' => 'Relación entre tutores y alumnos',
                'icono' => 'heart-handshake',
                'estado' => true,
            ],
            [
                'nombre' => 'Asignación de materias',
                'descripcion' => 'Asignación de materias a docentes por grupo',
                'icono' => 'clipboard-list',
                'estado' => true,
            ],



            [
                'nombre' => 'Asistencia',
                'descripcion' => 'Registro y consulta de asistencia',
                'icono' => 'calendar-check',
                'estado' => true,
            ],
            [
                'nombre' => 'Calificaciones',
                'descripcion' => 'Captura y consulta de calificaciones',
                'icono' => 'chart-bar',
                'estado' => true,
            ],
            [
                'nombre' => 'Correcciones de calificaciones',
                'descripcion' => 'Autorización y seguimiento de correcciones excepcionales',
                'icono' => 'file-pen-line',
                'estado' => true,
            ],
            [
                'nombre' => 'Avisos',
                'descripcion' => 'Publicación y consulta de avisos escolares',
                'icono' => 'megaphone',
                'estado' => true,
            ],
            [       
                'nombre' => 'Comentarios',
                'descripcion' => 'Consulta y moderación de comentarios',
                'icono' => 'message-circle',
                'estado' => true,
            ],
            [
                'nombre' => 'Permisos por rol',
                'descripcion' => 'Configuración de permisos por rol y módulo',
                'icono' => 'key',
                'estado' => true,
            ],
        ];

        foreach ($modulos as $modulo) {
            Modulo::updateOrCreate(
                ['nombre' => $modulo['nombre']],
                [
                    'descripcion' => $modulo['descripcion'],
                    'icono' => $modulo['icono'],
                    'estado' => $modulo['estado'],
                ]
            );
        }
    }
}