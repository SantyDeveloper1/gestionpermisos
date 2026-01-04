<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_permiso', function (Blueprint $table) {

            // Clave primaria (código institucional)
            $table->char('id_tipo_permiso', 13)->primary();

            // Datos descriptivos
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();

            // Reglas del permiso
            $table->boolean('requiere_recupero');
            $table->boolean('con_goce_haber');
            $table->boolean('requiere_documento')->default(false);

            // Estado lógico
            $table->tinyInteger('estado')->default(1);

            // Auditoría (recomendado a nivel profesional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_permiso');
    }
};