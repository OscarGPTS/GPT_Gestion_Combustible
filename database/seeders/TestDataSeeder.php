<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Project;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\FuelType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear conductores
        $drivers = [
            ['name' => 'JUAN CARLOS HERNANDEZ', 'license_number' => 'DL001', 'phone' => '5551234567'],
            ['name' => 'MANUEL CARDONA', 'license_number' => 'DL002', 'phone' => '5551234568'],
            ['name' => 'MARTIN MARTINEZ', 'license_number' => 'DL003', 'phone' => '5551234569'],
            ['name' => 'HECTOR AVILES', 'license_number' => 'DL004', 'phone' => '5551234570'],
            ['name' => 'JUAN CARLOS LOPEZ', 'license_number' => 'DL005', 'phone' => '5551234571'],
            ['name' => 'ALEJANDRO RODRIGUEZ', 'license_number' => 'DL006', 'phone' => '5551234572'],
        ];

        foreach ($drivers as $driver) {
            // Crear usuario para cada conductor
            $user = User::create([
                'name' => $driver['name'],
                'email' => strtolower(str_replace(' ', '.', $driver['name'])) . '@test.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Crear conductor
            Driver::create([
                'user_id' => $user->id,
                'name' => $driver['name'],
                'license_number' => $driver['license_number'],
                'phone' => $driver['phone'],
                'active' => true,
            ]);
        }

        // Crear proyectos
        $projects = [
            ['name' => 'GPT', 'code' => 'GPT', 'description' => 'Proyecto General'],
            ['name' => 'SU DOMICILIO', 'code' => 'SUDOM', 'description' => 'Su Domicilio'],
            ['name' => 'HOME DEPOT', 'code' => 'HD', 'description' => 'Home Depot'],
            ['name' => 'GASOLINERIA', 'code' => 'GAS', 'description' => 'Gasolinería'],
            ['name' => 'SAM\'S', 'code' => 'SAMS', 'description' => 'Sam\'s Club'],
            ['name' => 'VERIFICENTRO', 'code' => 'VER', 'description' => 'Verificentro'],
            ['name' => 'LIVERPOOL', 'code' => 'LIV', 'description' => 'Liverpool'],
            ['name' => 'EGSA', 'code' => 'EGSA', 'description' => 'EGSA'],
            ['name' => 'ARRENDADORA TURBO', 'code' => 'AT', 'description' => 'Arrendadora Turbo'],
            ['name' => 'MECANICO', 'code' => 'MEC', 'description' => 'Mecánico'],
            ['name' => 'AEROMEXICO CARGO', 'code' => 'AMC', 'description' => 'Aeroméxico Cargo'],
        ];

        foreach ($projects as $project) {
            Project::create([
                'name' => $project['name'],
                'code' => $project['code'],
                'description' => $project['description'],
                'active' => true,
            ]);
        }

        // Obtener tipos de combustible
        $gasolina = FuelType::where('name', 'gasoline')->first();
        $diesel = FuelType::where('name', 'diesel')->first();

        // Crear vehículos
        $vehicles = [
            ['unit' => 'TRANSIT', 'brand' => 'FORD', 'model' => 'TRANSIT', 'year' => 2020, 'plate' => 'ABC001', 'fuel_type_id' => $gasolina->id, 'tank_capacity' => 80, 'initial_mileage' => 256742, 'color' => 'Blanco'],
            ['unit' => 'AVEO-1', 'brand' => 'CHEVROLET', 'model' => 'AVEO', 'year' => 2018, 'plate' => 'ABC002', 'fuel_type_id' => $gasolina->id, 'tank_capacity' => 45, 'initial_mileage' => 172763, 'color' => 'Plata'],
            ['unit' => 'AVEO-2', 'brand' => 'CHEVROLET', 'model' => 'AVEO', 'year' => 2019, 'plate' => 'ABC003', 'fuel_type_id' => $gasolina->id, 'tank_capacity' => 45, 'initial_mileage' => 157618, 'color' => 'Plata'],
            ['unit' => 'F-350', 'brand' => 'FORD', 'model' => 'F-350', 'year' => 2019, 'plate' => 'ABC004', 'fuel_type_id' => $diesel->id, 'tank_capacity' => 100, 'initial_mileage' => 433035, 'color' => 'Blanco'],
            ['unit' => 'HINO', 'brand' => 'HINO', 'model' => 'HINO 300', 'year' => 2018, 'plate' => 'ABC005', 'fuel_type_id' => $diesel->id, 'tank_capacity' => 120, 'initial_mileage' => 97461, 'color' => 'Blanco'],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create([
                'unit' => $vehicle['unit'],
                'brand' => $vehicle['brand'],
                'model' => $vehicle['model'],
                'year' => $vehicle['year'],
                'plate' => $vehicle['plate'],
                'fuel_type_id' => $vehicle['fuel_type_id'],
                'tank_capacity' => $vehicle['tank_capacity'],
                'initial_mileage' => $vehicle['initial_mileage'],
                'color' => $vehicle['color'],
                'active' => true,
            ]);
        }
    }
}
