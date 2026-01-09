<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sesion_recuperacion', function (Blueprint $table) {
            // Eliminar columnas antiguas que ya no se usan
            $table->dropColumn(['asignatura', 'semestre', 'tipo_sesion', 'modalidad']);

            // Agregar columna para la relación con asignatura
            $table->char('idAsignatura', 13)->nullable()->after('id_plan');

            // Agregar foreign key
            $table->foreign('idAsignatura')
                ->references('idAsignatura')
                ->on('asignaturas')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesion_recuperacion', function (Blueprint $table) {
            // Eliminar foreign key
            $table->dropForeign(['idAsignatura']);

            // Eliminar columna
            $table->dropColumn('idAsignatura');

            // Restaurar columnas antiguas
            $table->string('asignatura', 100)->nullable();
            $table->enum('semestre', ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO', 'SEPTIMO', 'OCTAVO', 'NOVENO', 'DECIMO'])->nullable();
            $table->enum('tipo_sesion', ['TEORIA', 'PRACTICA', 'EXAMEN'])->nullable();
            $table->enum('modalidad', ['PRESENCIAL', 'VIRTUAL', 'EXTRA'])->nullable();
        });
    }
};
