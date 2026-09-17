<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla grupos.
     */
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            // Llave primaria del diagrama.
            $table->id('idGrupo');

            // Ejemplo: 1, 2, 3, 4, 5 o 6.
            $table->tinyInteger('grado');

            // Ejemplo: A, B, C.
            $table->char('letraGrupo', 1);

            // Ejemplo: 2026-2027.
            $table->string('cicloEsc', 25);

            // 1 = activo, 0 = inactivo.
            $table->boolean('estado')->default(true);

            /*
             * Maestro asignado.
             * grupos.idMaestro → usuarios.idUsuario
             */
            $table->unsignedBigInteger('idMaestro');

            /*
             * Escuela a la que pertenece el grupo.
             * grupos.escuela_idEscuela → escuelas.idEscuela
             */
            $table->unsignedBigInteger('escuela_idEscuela');

            $table->timestamps();

            $table->foreign('idMaestro', 'fk_grupo_maestro')
                ->references('idUsuario')
                ->on('usuarios')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('escuela_idEscuela', 'fk_grupo_escuela')
                ->references('idEscuela')
                ->on('escuelas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            /*
             * Evita tener dos grupos con la misma combinación:
             * grado + letra + ciclo escolar + escuela.
             */
            $table->unique(
                ['grado', 'letraGrupo', 'cicloEsc', 'escuela_idEscuela'],
                'grupo_unico_por_escuela'
            );
        });
    }

    /**
     * Elimina la tabla grupos.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};