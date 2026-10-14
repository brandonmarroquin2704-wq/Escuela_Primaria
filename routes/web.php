<?php

use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\EscuelaController;
use App\Http\Controllers\PeriodoEscolarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\TutorAlumnoController;
use App\Http\Controllers\AsignacionDocenteAsignaturaController;
use App\Http\Controllers\AccesoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudTutorController;
use App\Http\Controllers\SolicitudDocenteController;
use App\Http\Controllers\CalificacionDocenteController;
use App\Http\Controllers\BoletaTutorController;

Route::middleware(['auth', 'activa'])->prefix('tutor')->group(function () {
    Route::get('solicitudes', [SolicitudTutorController::class, 'index'])
        ->name('tutor.solicitudes.index');
            Route::get('hijos/{alumno}/boleta', [BoletaTutorController::class, 'show'])
        ->name('tutor.boleta.show');

    Route::post('solicitudes', [SolicitudTutorController::class, 'store'])
        ->name('tutor.solicitudes.store');
});


Route::middleware(['auth', 'activa'])->prefix('docente')->group(function () {
    Route::get('solicitudes', [SolicitudDocenteController::class, 'index'])
        ->name('docente.solicitudes.index');

    Route::post('solicitudes/{solicitud}/aprobar', [SolicitudDocenteController::class, 'aprobar'])
        ->name('docente.solicitudes.aprobar');

    Route::post('solicitudes/{solicitud}/rechazar', [SolicitudDocenteController::class, 'rechazar'])
        ->name('docente.solicitudes.rechazar');
            Route::get('calificaciones', [CalificacionDocenteController::class, 'index'])
        ->name('docente.calificaciones.index');

    Route::post('calificaciones', [CalificacionDocenteController::class, 'guardar'])
        ->name('docente.calificaciones.guardar');
});


Route::get('/', [AccesoController::class, 'inicio'])
    ->name('acceso.inicio');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'activa'])
    ->name('dashboard');


Route::middleware(['auth', 'activa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy']);
});


Route::middleware(['auth', 'activa'])->group(function () {
    Route::get('escuelas/create', [EscuelaController::class, 'create'])
        ->middleware('permiso:Escuelas,Registrar')
        ->name('escuelas.create');

    Route::post('escuelas', [EscuelaController::class, 'store'])
        ->middleware('permiso:Escuelas,Registrar')
        ->name('escuelas.store');

    Route::get('escuelas/{escuela}/edit', [EscuelaController::class, 'edit'])
        ->middleware('permiso:Escuelas,Editar')
        ->name('escuelas.edit');

    Route::match(['put', 'patch'], 'escuelas/{escuela}', [EscuelaController::class, 'update'])
        ->middleware('permiso:Escuelas,Editar')
        ->name('escuelas.update');

    // TEMPORAL: borrar cuando el menú lateral ya no enlace a /escuelas
    Route::get('escuelas', fn () => redirect()->route('dashboard'));
});


Route::resource('periodos-escolares', PeriodoEscolarController::class)
    ->middleware(['auth', 'activa']);


Route::resource('asignaturas', AsignaturaController::class)
    ->middleware(['auth', 'activa']);


// poder comentarlo para quitar los roles, ya quedaron en el sistema
Route::middleware(['auth', 'activa'])->group(function () {
    Route::get('roles', [RolController::class, 'index'])
        ->middleware('permiso:Roles,Consultar')
        ->name('roles.index');

    Route::get('roles/create', [RolController::class, 'create'])
        ->middleware('permiso:Roles,Registrar')
        ->name('roles.create');

    Route::post('roles', [RolController::class, 'store'])
        ->middleware('permiso:Roles,Registrar')
        ->name('roles.store');

    Route::get('roles/{role}/edit', [RolController::class, 'edit'])
        ->middleware('permiso:Roles,Editar')
        ->name('roles.edit');

    Route::match(['put', 'patch'], 'roles/{role}', [RolController::class, 'update'])
        ->middleware('permiso:Roles,Editar')
        ->name('roles.update');
});


Route::middleware(['auth', 'activa'])->group(function () {
    Route::get('usuarios', [UsuarioController::class, 'index'])
        ->middleware('permiso:Usuarios,Consultar')
        ->name('usuarios.index');

    Route::get('usuarios/create', [UsuarioController::class, 'create'])
        ->middleware('permiso:Usuarios,Registrar')
        ->name('usuarios.create');

    Route::post('usuarios', [UsuarioController::class, 'store'])
        ->middleware('permiso:Usuarios,Registrar')
        ->name('usuarios.store');

    Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])
        ->middleware('permiso:Usuarios,Editar')
        ->name('usuarios.edit');

    Route::match(['put', 'patch'], 'usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('permiso:Usuarios,Editar')
        ->name('usuarios.update');

    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])
        ->middleware('permiso:Usuarios,Eliminar')
        ->name('usuarios.destroy');
});


Route::middleware(['auth', 'activa'])->group(function () {
    Route::get('grupos', [GrupoController::class, 'index'])
        ->middleware('permiso:Grupos,Consultar')
        ->name('grupos.index');

    Route::get('grupos/create', [GrupoController::class, 'create'])
        ->middleware('permiso:Grupos,Registrar')
        ->name('grupos.create');

    Route::post('grupos', [GrupoController::class, 'store'])
        ->middleware('permiso:Grupos,Registrar')
        ->name('grupos.store');

    Route::get('grupos/{grupo}/edit', [GrupoController::class, 'edit'])
        ->middleware('permiso:Grupos,Editar')
        ->name('grupos.edit');

    Route::match(['put', 'patch'], 'grupos/{grupo}', [GrupoController::class, 'update'])
        ->middleware('permiso:Grupos,Editar')
        ->name('grupos.update');
});


Route::middleware(['auth', 'activa'])->group(function () {
    Route::get('alumnos', [AlumnoController::class, 'index'])
        ->middleware('permiso:Alumnos,Consultar')
        ->name('alumnos.index');

    Route::get('alumnos/create', [AlumnoController::class, 'create'])
        ->middleware('permiso:Alumnos,Registrar')
        ->name('alumnos.create');

    Route::post('alumnos', [AlumnoController::class, 'store'])
        ->middleware('permiso:Alumnos,Registrar')
        ->name('alumnos.store');

    Route::get('alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])
        ->middleware('permiso:Alumnos,Editar')
        ->name('alumnos.edit');

    Route::match(['put', 'patch'], 'alumnos/{alumno}', [AlumnoController::class, 'update'])
        ->middleware('permiso:Alumnos,Editar')
        ->name('alumnos.update');
});


Route::resource('tutor-alumnos', TutorAlumnoController::class)
    ->middleware(['auth', 'activa']);


Route::resource(
    'asignaciones-docentes',
    AsignacionDocenteAsignaturaController::class
)->middleware(['auth', 'activa']);


require __DIR__.'/auth.php';