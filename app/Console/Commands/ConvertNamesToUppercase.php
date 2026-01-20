<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Models\Project;
use Illuminate\Console\Command;

class ConvertNamesToUppercase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:convert-uppercase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte los nombres de conductores y proyectos a mayúsculas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Convirtiendo nombres a mayúsculas...');

        // Convertir nombres de conductores
        $drivers = Driver::all();
        $driverCount = 0;
        foreach ($drivers as $driver) {
            $upperName = mb_strtoupper($driver->name);
            if ($driver->name !== $upperName) {
                $driver->update(['name' => $upperName]);
                $driverCount++;
            }
        }
        $this->info("✓ Conductores actualizados: {$driverCount}");

        // Convertir nombres de proyectos
        $projects = Project::all();
        $projectCount = 0;
        foreach ($projects as $project) {
            $upperName = mb_strtoupper($project->name);
            if ($project->name !== $upperName) {
                $project->update(['name' => $upperName]);
                $projectCount++;
            }
        }
        $this->info("✓ Proyectos actualizados: {$projectCount}");

        $this->info('');
        $this->info('🎉 Conversión completada exitosamente!');

        return Command::SUCCESS;
    }
}
