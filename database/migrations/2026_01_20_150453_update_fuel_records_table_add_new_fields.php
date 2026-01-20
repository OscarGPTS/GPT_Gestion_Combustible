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
        Schema::table('fuel_records', function (Blueprint $table) {
            // Cambiar shift de enum a string para permitir texto libre (D/N)
            $table->string('shift', 50)->nullable()->change();
            
            // Agregar campo N/P (texto libre)
            $table->string('np_text', 100)->nullable()->after('shift');
            
            // Agregar campos separados para Gasolina y Diesel
            $table->decimal('gasoline_cost', 10, 2)->nullable()->after('km_per_liter');
            $table->decimal('diesel_cost', 10, 2)->nullable()->after('gasoline_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_records', function (Blueprint $table) {
            $table->dropColumn(['np_text', 'gasoline_cost', 'diesel_cost']);
            $table->enum('shift', ['day', 'night'])->default('day')->change();
        });
    }
};
