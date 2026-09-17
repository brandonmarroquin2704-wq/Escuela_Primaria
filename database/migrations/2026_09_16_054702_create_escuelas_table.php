<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla escuelas.
     */
    public function up(): void
    {
        Schema::create('escuelas', function (Blueprint $table) {
            // Llave primaria del diagrama.
            $table->id('idEscuela');

            // Datos generales de la escuela.
            $table->string('nombre', 100);

            // CCT = Clave del Centro de Trabajo.
            $table->string('claveCentroTrabajo', 25)->unique();

            $table->string('telefono', 25);

            // Se marca como único para evitar dos escuelas con el mismo correo.
            $table->string('correo', 45)->unique();

            $table->string('direccion', 200);
            $table->string('municipio', 80);
            $table->string('codigoPostal', 15);

            // true = activa, false = inactiva.
            $table->boolean('estado')->default(true);

            // Campos definidos en tu diagrama.
            $table->dateTime('fechaRegistro')->useCurrent();
            $table->dateTime('fechaActualizacion')->useCurrent();

            // Campos de auditoría estándar de Laravel:
            // created_at y updated_at.
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla escuelas si se revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('escuelas');
    }
};