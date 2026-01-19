<?php

namespace Database\Seeders;

use App\Models\FuelType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fuelTypes = [
            [
                'name' => 'gasoline',
                'display_name' => 'Gasolina',
            ],
            [
                'name' => 'diesel',
                'display_name' => 'Diésel',
            ],
        ];

        foreach ($fuelTypes as $fuelType) {
            FuelType::firstOrCreate(
                ['name' => $fuelType['name']],
                $fuelType
            );
        }
    }
}
