<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla puente entre usuarios/tutores y alumnos.
     */
    public function up(): void
    {
        Schema::create('tutor_alumnos', function (Blueprint $table) {
            // Llave primaria del diagrama.
            $table->id('idTutorAlumno');

            /*
             * Usuario que actúa como tutor.
             * tutor_alumnos.usuario_idUsuario → usuarios.idUsuario
             */
            $table->unsignedBigInteger('usuario_idUsuario');

            /*
             * Alumno relacionado con el tutor.
             * tutor_alumnos.alumno_idAlumno → alumnos.idAlumno
             */
            $table->unsignedBigInteger('alumno_idAlumno');

            // Ejemplos: Madre, Padre, Abuelo, Tutor legal.
            $table->string('parentesco', 25);

            // 1 = tutor responsable principal; 0 = tutor secundario.
            $table->boolean('esResponsable')->default(false);

            // 1 = relación activa; 0 = relación inactiva.
            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('usuario_idUsuario', 'fk_tutor_alumno_usuario')
                ->references('idUsuario')
                ->on('usuarios')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('alumno_idAlumno', 'fk_tutor_alumno_alumno')
                ->references('idAlumno')
                ->on('alumnos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            /*
             * Impide registrar dos veces al mismo tutor
             * para el mismo alumno.
             */
            $table->unique(
                ['usuario_idUsuario', 'alumno_idAlumno'],
                'tutor_alumno_unico'
            );
        });
    }

    /**
     * Elimina la tabla tutor_alumnos.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_alumnos');
    }
};