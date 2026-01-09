<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semestre_academico', function (Blueprint $table) {

            $table->char('IdSemestreAcademico', 13)->primary();

            $table->string('codigo_Academico', 10)->unique();
            $table->year('anio_academico');

            $table->date('FechaInicioAcademico');
            $table->date('FechaFinAcademico');

            $table->enum('EstadoAcademico', ['Planificado', 'Activo', 'Cerrado'])
                  ->default('Planificado');

            $table->boolean('EsActualAcademico')->default(0);

            $table->string('DescripcionAcademico', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semestre_academico');
    }
};