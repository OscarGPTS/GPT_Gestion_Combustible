<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehiclePerformance;
use Illuminate\Database\Seeder;

class VehiclePerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $performances = [
            ['unit' => 'AVEO-1', 'fuel_price' => 23.39, 'performance' => 11],
            ['unit' => 'F-350', 'fuel_price' => 23.39, 'performance' => 4],
            ['unit' => 'RANGERS', 'fuel_price' => 23.39, 'performance' => 6],
            ['unit' => 'AVEO-2', 'fuel_price' => 23.39, 'performance' => 13],
        ];

        foreach ($performances as $perf) {
            $vehicle = Vehicle::where('unit', $perf['unit'])->first();
            
            if ($vehicle) {
                VehiclePerformance::create([
                    'vehicle_id' => $vehicle->id,
                    'fuel_price' => $perf['fuel_price'],
                    'performance' => $perf['performance'],
                    'effective_date' => now(),
                    'current' => true,
                ]);
            }
        }
    }
}
