<?php

namespace App\Exports;

use App\Models\FuelRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

        // Filtrar por mes si se proporciona (YYYY-MM)
        if ($this->month) {
            [$year, $month] = explode('-', $this->month);
            $query->whereYear('date', (int) $year)
                  ->whereMonth('date', (int) $month);
        }

        // Filtrar por búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('vehicle', function ($v) {
                    $v->where('unit', 'like', '%' . $this->search . '%')
                      ->orWhere('plate', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('driver', function ($d) {
                    $d->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('folio', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('date', 'desc')->get()->map(function ($record) {
            return [
                'Folio' => $record->folio,
                'Unidad' => $record->vehicle->unit ?? '-',
                'Fecha' => optional($record->date)->format('d/m/Y'),
                'Regreso' => optional($record->return_date)->format('d/m/Y'),
                'Conductor' => $record->driver->name ?? '-',
                'D/N' => $record->shift,
                'N/P' => $record->np_text,
                'Proveedor o Cliente' => $record->provider_client,
                'Descripción' => $record->description,
                'Destino' => $record->destination,
                'Kilometraje inicial' => $record->initial_mileage,
                'Kilometraje final' => $record->final_mileage,
                'Kilómetros recorridos' => $record->mileage_traveled,
                'Consumo' => number_format($record->liters ?? 0, 2),
                'Costo' => number_format($record->cost ?? 0, 2),
                'Kilómetros por litro' => number_format($record->km_per_liter ?? 0, 2),
                '$ Gasolina' => number_format($record->gasoline_cost ?? 0, 2),
                '$ Diesel' => number_format($record->diesel_cost ?? 0, 2),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Unidad',
            'Fecha',
            'Regreso',
            'Conductor',
            'D/N',
            'N/P',
            'Proveedor o Cliente',
            'Descripción',
            'Destino',
            'Kilometraje inicial',
            'Kilometraje final',
            'Kilómetros recorridos',
            'Consumo',
            'Costo',
            'Kilómetros por litro',
            '$ Gasolina',
            '$ Diesel',
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFE699'], // Amarillo
                ],
            ],
        ];
    }
}
