<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plan_recuperacion', function (Blueprint $table) {
            // Clave primaria
            $table->char('id_plan', 13)->primary();

            // FK a permiso (debe coincidir con el tipo de id_permiso en tabla permiso)
            $table->char('id_permiso', 13)->unique();

            $table->date('fecha_presentacion');

            $table->decimal('total_horas_recuperar', 5, 2);

            $table->enum('estado_plan', [
                'PRESENTADO',
                'APROBADO',
                'OBSERVADO'
            ]);

            $table->text('observacion')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('id_permiso')
                ->references('id_permiso')
                ->on('permiso')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_recuperacion');
    }
};