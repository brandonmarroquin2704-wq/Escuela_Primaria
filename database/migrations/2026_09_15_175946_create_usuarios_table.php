<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            // Llave primaria: idUsuario INT AUTO_INCREMENT
            $table->id('idUsuario');

            // Datos personales
            $table->string('nombre', 45);
            $table->string('apellidoPaterno', 45);
            $table->string('apellidoMaterno', 45);

            // Datos de acceso
            $table->string('correo', 20)->unique();

            // No uses varchar(20): Laravel guardará un hash de contraseña.
            $table->string('contrasena');

            // Relación con la tabla roles.
            $table->unsignedBigInteger('rol_idRol');

            // Fecha de registro y estado del usuario.
            $table->date('fRegistro');
            $table->boolean('estado')->default(true);

            // Crea created_at y updated_at.
            $table->timestamps();

            // La tabla roles debe existir antes de ejecutar esta migración.
            $table->foreign('rol_idRol')
                ->references('idRol')
                ->on('roles')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};