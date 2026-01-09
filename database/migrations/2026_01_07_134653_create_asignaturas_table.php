<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asignaturas', function (Blueprint $table) {

            $table->char('idAsignatura', 13)->primary();

            $table->string('codigo_asignatura', 20)->unique();
            $table->string('nom_asignatura', 120);

            $table->integer('creditos');
            $table->integer('horas_teoria')->default(0);
            $table->integer('horas_practica')->default(0);

            // FK - CICLOS (IdCiclo)
            $table->char('IdCiclo', 13);

            $table->string('tipo', 20)->nullable();

            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');

            $table->timestamps();

            // FOREIGN KEYS
            $table->foreign('IdCiclo')
                ->references('IdCiclo')
                ->on('ciclos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaturas');
    }
};