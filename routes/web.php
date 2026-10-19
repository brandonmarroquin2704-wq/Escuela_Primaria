<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AsignacionDocenteAsignaturaController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\BoletaTutorController;
use App\Http\Controllers\CalificacionDocenteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EscuelaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PeriodoEscolarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SolicitudDocenteController;
use App\Http\Controllers\SolicitudTutorController;
use App\Http\Controllers\TutorAlumnoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Pública
Route::get('/', [AccesoController::class, 'inicio'])->name('acceso.inicio');

// Todo lo demás requiere sesión y cuenta activa
Route::middleware(['auth', 'activa'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Tutor
    Route::prefix('tutor')->group(function () {
        Route::get('solicitudes', [SolicitudTutorController::class, 'index'])->name('tutor.solicitudes.index');
        Route::post('solicitudes', [SolicitudTutorController::class, 'store'])->name('tutor.solicitudes.store');
        Route::get('hijos/{alumno}/boleta', [BoletaTutorController::class, 'show'])->name('tutor.boleta.show');
    });

    // Docente
    Route::prefix('docente')->group(function () {
        Route::controller(SolicitudDocenteController::class)->group(function () {
            Route::get('solicitudes', 'index')->name('docente.solicitudes.index');
            Route::post('solicitudes/{solicitud}/aprobar', 'aprobar')->name('docente.solicitudes.aprobar');
            Route::post('solicitudes/{solicitud}/rechazar', 'rechazar')->name('docente.solicitudes.rechazar');
        });

        Route::controller(CalificacionDocenteController::class)->group(function () {
            Route::get('calificaciones', 'index')->name('docente.calificaciones.index');
            Route::post('calificaciones', 'guardar')->name('docente.calificaciones.guardar');
        });
    });

        // Mi cuenta
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Escuelas
    Route::controller(EscuelaController::class)->group(function () {
        Route::get('escuelas/create', 'create')->middleware('permiso:Escuelas,Registrar')->name('escuelas.create');
        Route::post('escuelas', 'store')->middleware('permiso:Escuelas,Registrar')->name('escuelas.store');
        Route::get('escuelas/{escuela}/edit', 'edit')->middleware('permiso:Escuelas,Editar')->name('escuelas.edit');
        Route::match(['put', 'patch'], 'escuelas/{escuela}', 'update')->middleware('permiso:Escuelas,Editar')->name('escuelas.update');
    });

        Route::get('escuela', [EscuelaController::class, 'show'])->name('escuelas.show');

    // Roles (puedes comentar este bloque si ya no editas roles desde la interfaz)
    Route::controller(RolController::class)->group(function () {
        Route::get('roles', 'index')->middleware('permiso:Roles,Consultar')->name('roles.index');
        Route::get('roles/create', 'create')->middleware('permiso:Roles,Registrar')->name('roles.create');
        Route::post('roles', 'store')->middleware('permiso:Roles,Registrar')->name('roles.store');
        Route::get('roles/{role}/edit', 'edit')->middleware('permiso:Roles,Editar')->name('roles.edit');
        Route::match(['put', 'patch'], 'roles/{role}', 'update')->middleware('permiso:Roles,Editar')->name('roles.update');
    });

    // Usuarios
    Route::controller(UsuarioController::class)->group(function () {
        Route::get('usuarios', 'index')->middleware('permiso:Usuarios,Consultar')->name('usuarios.index');
        Route::get('usuarios/create', 'create')->middleware('permiso:Usuarios,Registrar')->name('usuarios.create');
        Route::post('usuarios', 'store')->middleware('permiso:Usuarios,Registrar')->name('usuarios.store');
        Route::get('usuarios/{usuario}/edit', 'edit')->middleware('permiso:Usuarios,Editar')->name('usuarios.edit');
        Route::match(['put', 'patch'], 'usuarios/{usuario}', 'update')->middleware('permiso:Usuarios,Editar')->name('usuarios.update');
        Route::delete('usuarios/{usuario}', 'destroy')->middleware('permiso:Usuarios,Eliminar')->name('usuarios.destroy');
    });

    // Grupos
    Route::controller(GrupoController::class)->group(function () {
        Route::get('grupos', 'index')->middleware('permiso:Grupos,Consultar')->name('grupos.index');
        Route::get('grupos/create', 'create')->middleware('permiso:Grupos,Registrar')->name('grupos.create');
        Route::post('grupos', 'store')->middleware('permiso:Grupos,Registrar')->name('grupos.store');
        Route::get('grupos/{grupo}/edit', 'edit')->middleware('permiso:Grupos,Editar')->name('grupos.edit');
        Route::match(['put', 'patch'], 'grupos/{grupo}', 'update')->middleware('permiso:Grupos,Editar')->name('grupos.update');
    });

    //Asignaturas
    Route::controller(AsignaturaController::class)->group(function () {
        Route::get('asignaturas', 'index')
            ->middleware('permiso:Asignaturas,Consultar')
            ->name('asignaturas.index');

        Route::get('asignaturas/create', 'create')
            ->middleware('permiso:Asignaturas,Registrar')
            ->name('asignaturas.create');

        Route::post('asignaturas', 'store')
            ->middleware('permiso:Asignaturas,Registrar')
            ->name('asignaturas.store');

        Route::get('asignaturas/{asignatura}/edit', 'edit')
            ->middleware('permiso:Asignaturas,Editar')
            ->name('asignaturas.edit');

        Route::match(['put', 'patch'], 'asignaturas/{asignatura}', 'update')
            ->middleware('permiso:Asignaturas,Editar')
            ->name('asignaturas.update');

        Route::delete('asignaturas/{asignatura}', 'destroy')
            ->middleware('permiso:Asignaturas,Eliminar')
            ->name('asignaturas.destroy');
    });

    // Alumnos
    Route::controller(AlumnoController::class)->group(function () {
        Route::get('alumnos', 'index')->middleware('permiso:Alumnos,Consultar')->name('alumnos.index');
        Route::get('alumnos/create', 'create')->middleware('permiso:Alumnos,Registrar')->name('alumnos.create');
        Route::post('alumnos', 'store')->middleware('permiso:Alumnos,Registrar')->name('alumnos.store');
        Route::get('alumnos/{alumno}/edit', 'edit')->middleware('permiso:Alumnos,Editar')->name('alumnos.edit');
        Route::match(['put', 'patch'], 'alumnos/{alumno}', 'update')->middleware('permiso:Alumnos,Editar')->name('alumnos.update');
    });

    // Módulos con CRUD completo
    Route::resource('periodos-escolares', PeriodoEscolarController::class);
    Route::resource('tutor-alumnos', TutorAlumnoController::class);
    Route::resource('asignaciones-docentes', AsignacionDocenteAsignaturaController::class);
});

require __DIR__.'/auth.php';