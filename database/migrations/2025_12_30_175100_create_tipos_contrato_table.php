<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_contrato', function (Blueprint $table) {
            $table->char('idTipo_contrato', 13)->primary(); // PK CHAR(13)
            $table->string('nombre', 50);                   // VARCHAR(50)
            $table->timestamps();                           // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_contrato');
    }
};