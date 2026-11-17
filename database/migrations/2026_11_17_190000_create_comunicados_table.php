<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asignacion_id')->index();
            $table->unsignedBigInteger('docente_idUsuario')->index();
            $table->string('tipo', 10);
            $table->string('titulo', 120);
            $table->text('contenido');
            $table->date('fecha_entrega')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados');
    }
};