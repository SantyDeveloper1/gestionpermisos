<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ciclos', function (Blueprint $table) {

            $table->char('IdCiclo', 13)->primary();       // PK
            $table->string('NombreCiclo', 100);           // Ej. "Ciclo Básico"
            
            // NÚMERO DE CICLO EN ROMANOS (I, II, III, IV, V...)
            $table->string('NumeroCiclo', 10);            // texto corto, suficiente para romanos
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('ciclos');
    }
};