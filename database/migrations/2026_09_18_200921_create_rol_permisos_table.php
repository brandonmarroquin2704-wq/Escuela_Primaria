<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id('idRolPermiso');

            $table->unsignedBigInteger('rol_idRol');

            $table->unsignedBigInteger('modulo_idModulo');

            $table->unsignedBigInteger('permiso_idPermiso');

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('rol_idRol', 'fk_rol_permiso_rol')
                ->references('idRol')
                ->on('roles')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('modulo_idModulo', 'fk_rol_permiso_modulo')
                ->references('idModulo')
                ->on('modulos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('permiso_idPermiso', 'fk_rol_permiso_permiso')
                ->references('idPermiso')
                ->on('permisos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->unique(
                ['rol_idRol', 'modulo_idModulo', 'permiso_idPermiso'],
                'rol_modulo_permiso_unico'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permisos');
    }
};