<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permiso', function (Blueprint $table) {

            // Clave primaria
            $table->char('id_permiso', 13)->primary();

            // Relaciones
            $table->char('id_docente', 13);
            $table->char('id_tipo_permiso', 13);

            // Periodo del permiso
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // Cálculos administrativos
            $table->integer('dias_permiso');
            $table->decimal('horas_afectadas', 5, 2);

            // Estado del flujo
            $table->enum('estado_permiso', [
                'SOLICITADO',
                'APROBADO',
                'RECHAZADO',
                'EN_RECUPERACION',
                'RECUPERADO',
                'CERRADO'
            ]);

            // Detalles
            $table->text('motivo')->nullable();
            $table->text('observacion')->nullable();

            // Fechas de control
            $table->date('fecha_solicitud');
            $table->date('fecha_resolucion')->nullable();

            // Auditoría
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_docente')
                ->references('idDocente')
                ->on('docentes')
                ->onDelete('restrict');

            $table->foreign('id_tipo_permiso')
                ->references('id_tipo_permiso')
                ->on('tipo_permiso')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permiso');
    }
};
