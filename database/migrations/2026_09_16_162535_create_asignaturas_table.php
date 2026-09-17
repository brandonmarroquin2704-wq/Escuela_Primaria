<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla asignaturas.
     */
    public function up(): void
    {
        Schema::create('asignaturas', function (Blueprint $table) {
            // Llave primaria del diagrama.
            $table->id('idAsignatura');

            $table->string('nombre', 85);

            $table->string('descripcion', 200)->nullable();

            // true = activa; false = inactiva.
            $table->boolean('estado')->default(true);

            // Fechas definidas en el diagrama.
            $table->dateTime('fechaRegistro')->useCurrent();
            $table->dateTime('fechaActualizacion')->useCurrent();

            // Campos técnicos de Laravel: created_at y updated_at.
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla si se revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaturas');
    }
};