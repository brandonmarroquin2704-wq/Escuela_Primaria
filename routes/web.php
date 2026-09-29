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

Route::get('/', [AccesoController::class, 'inicio'])
    ->name('acceso.inicio');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy']);
});

Route::resource('escuelas', EscuelaController::class)
    ->middleware('auth');

Route::resource('periodos-escolares', PeriodoEscolarController::class)
    ->middleware('auth');

Route::resource('asignaturas', AsignaturaController::class)
    ->middleware('auth');

    //poder comentarlo para quitar los roles, ya quedaron en el sistema
Route::middleware(['auth'])->group(function () {
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

Route::middleware(['auth'])->group(function () {
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

    Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('permiso:Usuarios,Editar')
        ->name('usuarios.update');

    Route::patch('usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('permiso:Usuarios,Editar')
        ->name('usuarios.update');

    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])
        ->middleware('permiso:Usuarios,Eliminar')
        ->name('usuarios.destroy');
});
Route::middleware(['auth'])->group(function () {
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

Route::middleware(['auth'])->group(function () {
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

    Route::put('alumnos/{alumno}', [AlumnoController::class, 'update'])
        ->middleware('permiso:Alumnos,Editar')
        ->name('alumnos.update');

    Route::patch('alumnos/{alumno}', [AlumnoController::class, 'update'])
        ->middleware('permiso:Alumnos,Editar')
        ->name('alumnos.update');
});

Route::resource('tutor-alumnos', TutorAlumnoController::class)
    ->middleware('auth');

Route::resource(
    'asignaciones-docentes',
    AsignacionDocenteAsignaturaController::class
)->middleware('auth');
require __DIR__.'/auth.php';