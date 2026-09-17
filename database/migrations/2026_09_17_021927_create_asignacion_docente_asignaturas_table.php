<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion_docente_asignaturas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('grupo_idGrupo');

            $table->unsignedBigInteger('asignatura_idAsignatura');

            $table->unsignedBigInteger('docente_idUsuario');

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('grupo_idGrupo', 'fk_asignacion_grupo')
                ->references('idGrupo')
                ->on('grupos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('asignatura_idAsignatura', 'fk_asignacion_asignatura')
                ->references('idAsignatura')
                ->on('asignaturas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('docente_idUsuario', 'fk_asignacion_docente')
                ->references('idUsuario')
                ->on('usuarios')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->unique(
                ['grupo_idGrupo', 'asignatura_idAsignatura'],
                'asignacion_grupo_asignatura_unica'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_docente_asignaturas');
    }
};