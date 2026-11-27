<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_acceso_salon', function (Blueprint $table) {
            $table->unsignedTinyInteger('intentos')->default(1)->after('estadoSolicitud');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_acceso_salon', function (Blueprint $table) {
            $table->dropColumn('intentos');
        });
    }
};