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
        Schema::create('fuel_records', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique(); // Folio
            $table->foreignId('vehicle_id')->constrained()->onDelete('restrict');
            $table->foreignId('driver_id')->constrained()->onDelete('restrict');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date'); // Fecha
            $table->date('return_date')->nullable(); // Regreso
            $table->enum('shift', ['day', 'night'])->default('day'); // D/N (día/noche)
            $table->string('provider_client')->nullable(); // Proveedor o cliente (N/P)
            $table->text('description')->nullable(); // Descripción
            $table->string('destination')->nullable(); // Destino
            $table->integer('initial_mileage'); // Kilometraje inicial
            $table->integer('final_mileage'); // Kilometraje final
            $table->integer('mileage_traveled')->storedAs('final_mileage - initial_mileage'); // Kilometros recorridos (calculado)
            $table->decimal('liters', 8, 2); // Consumo (litros)
            $table->decimal('fuel_price', 10, 2); // Precio gasolina/diesel
            $table->decimal('cost', 10, 2); // Costo total
            $table->decimal('km_per_liter', 8, 2)->nullable(); // Kilómetros x litro
            $table->decimal('amount', 10, 2)->nullable(); // Monto adicional
            $table->json('evidence')->nullable(); // Evidencia (imágenes)
            $table->timestamps();
            
            $table->index(['vehicle_id', 'date']);
            $table->index(['driver_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_records');
    }
};
