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
        Schema::table('plan_recuperacion', function (Blueprint $table) {
            $table->string('estado_notificado')->nullable()->after('estado_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_recuperacion', function (Blueprint $table) {
            $table->dropColumn('estado_notificado');
        });
    }
};
