<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->char('idDocente', 13)->primary();
            // 🔗 Relación con users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('codigo_unamba', 15)->unique()->nullable();
            $table->char('grado_id', 13);
            $table->char('categoria_id', 13);
            $table->char('tipo_contrato_id', 13);
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();

            // Foreign keys académicos
            $table->foreign('grado_id')->references('idGrados_academicos')->on('grados_academicos');
            $table->foreign('categoria_id')->references('idCategori_docente')->on('categorias_docente');
            $table->foreign('tipo_contrato_id')->references('idTipo_contrato')->on('tipos_contrato');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};