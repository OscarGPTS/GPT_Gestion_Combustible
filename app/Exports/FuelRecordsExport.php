<?php

namespace App\Exports;

use App\Models\FuelRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class FuelRecordsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $month;
    protected $search;

    public function __construct($month = null, $search = null)
    {
        $this->month = $month;
        $this->search = $search;
    }

    public function collection()
    {
        $query = FuelRecord::with('vehicle', 'driver', 'project');

        // Filtrar por mes si se proporciona
        if ($this->month) {
            $query->whereMonth('date', explode('-', $this->month)[1])
                  ->whereYear('date', explode('-', $this->month)[0]);
        }

        // Filtrar por búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('vehicle', function ($v) {
                    $v->where('unit', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('driver', function ($d) {
                    $d->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('folio', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get()->map(function ($record) {
            return [
                'Folio' => $record->folio,
                'Unidad' => $record->vehicle->unit,
                'Fecha' => $record->date->format('d/m/Y'),
                'Conductor' => $record->driver->name,
                'KM Iniciales' => $record->initial_mileage,
                'KM Finales' => $record->final_mileage,
                'KM Recorridos' => $record->mileage_traveled,
                'Litros' => number_format($record->liters, 2),
                'Precio/Litro' => number_format($record->fuel_price ?? 0, 2),
                'Costo Total' => number_format($record->cost, 2),
                'KM/L' => number_format($record->km_per_liter, 2),
                'Destino' => $record->destination ?? '-',
                'Descripción' => $record->description ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Unidad',
            'Fecha',
            'Conductor',
            'KM Iniciales',
            'KM Finales',
            'KM Recorridos',
            'Litros',
            'Precio/Litro',
            'Costo Total',
            'KM/L',
            'Destino',
            'Descripción',
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'DC2626'], // Rojo corporativo
                ],
            ],
        ];
    }
}
