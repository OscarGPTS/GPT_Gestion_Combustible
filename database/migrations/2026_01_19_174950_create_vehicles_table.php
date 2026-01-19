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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('unit'); // Unidad/Identificador del vehículo
            $table->string('brand'); // Marca
            $table->string('model'); // Modelo
            $table->year('year')->nullable(); // Año
            $table->string('plate')->unique(); // Placa
            $table->foreignId('fuel_type_id')->constrained()->onDelete('restrict');
            $table->decimal('tank_capacity', 8, 2)->nullable(); // Capacidad del tanque
            $table->integer('initial_mileage')->default(0); // Kilometraje inicial
            $table->string('color')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
