<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_tutores', function (Blueprint $table) {
            $table->bigIncrements('idDatosTutor');

            $table->unsignedBigInteger('usuario_idUsuario');

            $table->string('tipoResponsable', 20);
            $table->string('telefono', 25);

            $table->string('nombreReferencia', 150)->nullable();
            $table->string('telefonoReferencia', 25)->nullable();
            $table->string('relacionReferencia', 45)->nullable();

            $table->dateTime('fRegistro')->useCurrent();

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->unique(
                'usuario_idUsuario',
                'uq_datos_tutor_usuario'
            );

            $table->foreign('usuario_idUsuario')
                ->references('idUsuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_tutores');
    }
};