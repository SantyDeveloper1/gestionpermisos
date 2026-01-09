<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permiso', function (Blueprint $table) {
            // Campo para subir documento que sustenta el permiso
            $table->string('documento_sustento', 255)->nullable()->after('observacion');

            // Relación con semestre_academico
            $table->char('id_semestre_academico', 13)->nullable()->after('id_tipo_permiso');

            // Foreign key hacia semestre_academico
            $table->foreign('id_semestre_academico')
                ->references('IdSemestreAcademico')
                ->on('semestre_academico')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permiso', function (Blueprint $table) {
            // Eliminar foreign key primero
            $table->dropForeign(['id_semestre_academico']);

            // Eliminar columnas
            $table->dropColumn('id_semestre_academico');
            $table->dropColumn('documento_sustento');
        });
    }
};
