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
        Schema::create('vehicle_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->decimal('fuel_price', 10, 2); // Precio gasolina/diesel
            $table->decimal('performance', 8, 2); // Rendimiento (km x litro)
            $table->dateTime('effective_date'); // Fecha efectiva
            $table->boolean('current')->default(false); // Actual/vigente
            $table->timestamps();

            $table->index(['vehicle_id', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_performances');
    }
};
