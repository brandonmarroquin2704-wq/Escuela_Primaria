<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_acceso_salon', function (Blueprint $table) {
            $table->bigIncrements('idSolicitudAccesoSalon');

            $table->unsignedBigInteger('tutor_idUsuario');
            $table->unsignedBigInteger('alumno_idAlumno');
            $table->unsignedBigInteger('docente_validador_idUsuario')->nullable();

            $table->string('parentesco', 45);
            $table->boolean('esResponsable')->default(false);

            $table->string('estadoSolicitud', 20)->default('pendiente');

            $table->dateTime('fechaSolicitud');
            $table->dateTime('fechaRespuesta')->nullable();

            $table->text('observacionRespuesta')->nullable();

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->unique(
                ['tutor_idUsuario', 'alumno_idAlumno'],
                'uq_solicitud_tutor_alumno'
            );

            $table->foreign('tutor_idUsuario')
                ->references('idUsuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('alumno_idAlumno')
                ->references('idAlumno')
                ->on('alumnos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('docente_validador_idUsuario')
                ->references('idUsuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_acceso_salon');
    }
};