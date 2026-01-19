<?php

namespace App\Console\Commands;

use App\Models\FuelRecord;
use Illuminate\Console\Command;

class UpdateFuelRecordsDates extends Command
{
    protected $signature = 'fuel:update-dates';
    protected $description = 'Update fuel records dates to current month';

    public function handle()
    {
        $records = FuelRecord::all();
        foreach($records as $record) {
            $record->date = $record->date->addMonths(1);
            $record->save();
        }
        
        $this->info('✓ Registros actualizados a enero 2026');
        return Command::SUCCESS;
    }
}
