<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el enum de estado_sesion para agregar REPROGRAMADA
        DB::statement("ALTER TABLE sesion_recuperacion MODIFY COLUMN estado_sesion ENUM('PROGRAMADA', 'REPROGRAMADA', 'REALIZADA', 'CANCELADA') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al enum original sin REPROGRAMADA
        DB::statement("ALTER TABLE sesion_recuperacion MODIFY COLUMN estado_sesion ENUM('PROGRAMADA', 'REALIZADA', 'CANCELADA') NOT NULL");
    }
};
