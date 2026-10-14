<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id('idCalificacion');
            $table->decimal('valor', 4, 2);
            $table->string('observacion', 100)->nullable();
            $table->dateTime('fechaRegistro');
            $table->dateTime('fechaLimiteEdicion');
            $table->boolean('estado')->default(true);

            $table->foreignId('alumno_idAlumno')
                ->constrained('alumnos', 'idAlumno')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('periodo_escolar_idPeriodo')
                ->constrained('periodos_escolares', 'idPeriodo')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('asignatura_idAsignatura')
                ->constrained('asignaturas', 'idAsignatura')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('usuario_idUsuario')
                ->constrained('usuarios', 'idUsuario')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['alumno_idAlumno', 'asignatura_idAsignatura', 'periodo_escolar_idPeriodo'],
                'calificacion_unica'
            );
        });

        DB::statement(
            'ALTER TABLE calificaciones ADD CONSTRAINT chk_calificacion_valor CHECK (valor >= 0 AND valor <= 10)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};