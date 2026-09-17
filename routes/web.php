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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
Route::resource('roles', RolController::class)
    ->middleware('auth');

Route::resource('usuarios', UsuarioController::class)
    ->middleware('auth');

Route::resource('grupos', GrupoController::class)
    ->middleware('auth');

Route::resource('alumnos', AlumnoController::class)
    ->middleware('auth');

Route::resource('tutor-alumnos', TutorAlumnoController::class)
    ->middleware('auth');

Route::resource(
    'asignaciones-docentes',
    AsignacionDocenteAsignaturaController::class
)->middleware('auth');
require __DIR__.'/auth.php';