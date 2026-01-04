<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sesion_recuperacion', function (Blueprint $table) {

            // PK
            $table->char('id_sesion', 13)->primary();

            // FK
            $table->char('id_plan', 13);

            $table->date('fecha_sesion');

            $table->enum('modalidad', [
                'PRESENCIAL',
                'VIRTUAL',
                'EXTRA'
            ]);

            $table->enum('tipo_sesion', [
                'TEORIA',
                'PRACTICA',
                'EXAMEN'
            ]);

            $table->string('aula', 50)->nullable();

            $table->string('asignatura', 100)->nullable();

            // 🔹 NUEVO CAMPO
            $table->enum('semestre', [
                'PRIMERO',
                'SEGUNDO',
                'TERCERO',
                'CUARTO',
                'QUINTO',
                'SEXTO',
                'SEPTIMO',
                'OCTAVO',
                'NOVENO',
                'DECIMO'
            ]);

            $table->decimal('horas_recuperadas', 5, 2);

            $table->enum('estado_sesion', [
                'PROGRAMADA',
                'REALIZADA',
                'CANCELADA'
            ]);

            $table->timestamps();

            // FK
            $table->foreign('id_plan')
                ->references('id_plan')
                ->on('plan_recuperacion')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesion_recuperacion');
    }
};
