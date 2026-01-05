<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evidencia_recuperacion', function (Blueprint $table) {

            // Identificadores
            $table->char('id_evidencia', 13);
            $table->char('id_sesion', 13);

            // Tipo de evidencia
            $table->enum('tipo_evidencia', [
                'ACTA',
                'ASISTENCIA',
                'CAPTURA',
                'OTRO'
            ])->comment('Tipo de sustento de recuperación');

            // Archivo adjunto
            $table->string('archivo', 255);

            // Descripción opcional
            $table->string('descripcion', 255)->nullable();

            // Fecha de subida
            $table->timestamp('fecha_subida')->useCurrent();

            // Clave primaria compuesta
            $table->primary(['id_evidencia', 'id_sesion']);

            // Clave foránea
            $table->foreign('id_sesion')
                ->references('id_sesion')
                ->on('sesion_recuperacion')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencia_recuperacion');
    }
};
