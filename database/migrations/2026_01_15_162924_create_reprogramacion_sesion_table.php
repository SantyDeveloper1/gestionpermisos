<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reprogramacion_sesion', function (Blueprint $table) {

            // PK no incremental
            $table->char('id_reprogramacion', 13)->primary();

            // FK con el MISMO tipo
            $table->char('id_sesion', 13);

            $table->foreign('id_sesion')
                ->references('id_sesion')
                ->on('sesion_recuperacion')
                ->onDelete('cascade');

            // Datos anteriores
            $table->date('fecha_anterior');
            $table->time('hora_inicio_anterior');
            $table->time('hora_fin_anterior');
            $table->string('aula_anterior', 50);

            // Datos nuevos
            $table->date('fecha_nueva');
            $table->time('hora_inicio_nueva');
            $table->time('hora_fin_nueva');
            $table->string('aula_nueva', 50);

            // Control
            $table->text('motivo');
            $table->timestamp('fecha_registro')->useCurrent();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('reprogramacion_sesion');
    }
};
