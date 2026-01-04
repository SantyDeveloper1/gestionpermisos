<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categorias_docente', function (Blueprint $table) {
            $table->char('idCategori_docente', 13)->primary();   // PK CHAR(13)
            $table->string('nombre', 100); // Auxiliar, Asociado, Principal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_docente');
    }
};

