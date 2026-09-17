<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de periodos escolares.
     */
    public function up(): void
    {
        Schema::create('periodos_escolares', function (Blueprint $table) {
            // Llave primaria del diagrama.
            $table->id('idPeriodo');

            $table->date('fechaInicio');
            $table->date('fechaFin');

            // 1 = activo; 0 = inactivo.
            $table->boolean('estado')->default(true);

            $table->string('cicloEscolar', 40);
            $table->string('nombre', 45);

            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla si se revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos_escolares');
    }
};