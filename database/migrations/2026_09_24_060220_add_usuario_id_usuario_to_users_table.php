<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_idUsuario')
                ->nullable()
                ->unique()
                ->after('id');

            $table->foreign('usuario_idUsuario')
                ->references('idUsuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['usuario_idUsuario']);
            $table->dropUnique(['usuario_idUsuario']);
            $table->dropColumn('usuario_idUsuario');
        });
    }
};  