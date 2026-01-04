<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados_academicos', function (Blueprint $table) {
            $table->char('idGrados_academicos', 13)->primary(); // PK CHAR(13)
            $table->string('nombre', 100); // Ej.: Ing., Mg., Dr.
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados_academicos');
    }
};
