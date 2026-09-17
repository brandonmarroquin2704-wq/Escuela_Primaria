<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla alumnos.
     */
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            // Llave primaria del diagrama.
            $table->id('idAlumno');

            $table->string('nombre', 20);
            $table->string('apellidoPaterno', 35);
            $table->string('apellidoMaterno', 45);

            // Fecha de nacimiento.
            $table->date('fNac');

            // 1 = alumno activo; 0 = alumno inactivo.
            $table->boolean('estado')->default(true);

            $table->date('fechaRegistro')->useCurrent();

            /*
             * Grupo al que pertenece el alumno.
             * alumnos.grupo_idGrupo → grupos.idGrupo
             */
            $table->unsignedBigInteger('grupo_idGrupo');

            $table->timestamps();

            $table->foreign('grupo_idGrupo', 'fk_alumno_grupo')
                ->references('idGrupo')
                ->on('grupos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Elimina la tabla alumnos.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};