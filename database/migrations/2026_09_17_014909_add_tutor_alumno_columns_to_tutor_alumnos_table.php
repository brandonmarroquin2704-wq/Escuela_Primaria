<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutor_alumnos', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_idUsuario')
                ->after('id');

            $table->unsignedBigInteger('alumno_idAlumno')
                ->after('usuario_idUsuario');

            $table->string('parentesco', 25)
                ->after('alumno_idAlumno');

            $table->boolean('esResponsable')
                ->default(false)
                ->after('parentesco');

            $table->boolean('estado')
                ->default(true)
                ->after('esResponsable');

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

            $table->unique(
                ['usuario_idUsuario', 'alumno_idAlumno'],
                'tutor_alumno_unico'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tutor_alumnos', function (Blueprint $table) {
            $table->dropUnique('tutor_alumno_unico');

            $table->dropForeign('fk_tutor_alumno_usuario');
            $table->dropForeign('fk_tutor_alumno_alumno');

            $table->dropColumn([
                'usuario_idUsuario',
                'alumno_idAlumno',
                'parentesco',
                'esResponsable',
                'estado',
            ]);
        });
    }
};