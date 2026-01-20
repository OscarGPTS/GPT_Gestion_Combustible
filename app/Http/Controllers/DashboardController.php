<?php

namespace App\Http\Controllers;

use App\Exports\FuelRecordsExport;
use App\Models\Driver;
use App\Models\FuelRecord;
use App\Models\Project;
use App\Models\Vehicle;
use App\Models\VehiclePerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * Display dashboard with general report.
     */
    public function index(Request $request)
    {
        $query = FuelRecord::with(['vehicle', 'driver', 'project']);

        // Apply filters (default: show ALL records, filter only when user applies)
        $year = $request->input('year');
        $month = $request->input('month');
        $monthCompound = $request->input('month_compound'); // support legacy YYYY-MM param if provided

        if ($year && $month) {
            $query->whereYear('date', (int)$year)
                  ->whereMonth('date', (int)$month);
        } elseif ($monthCompound) {
            $date = \Carbon\Carbon::parse($monthCompound);
            $query->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($q) use ($search) {
                      $q->where('unit', 'like', "%{$search}%")
                        ->orWhere('plate', 'like', "%{$search}%");
                  })
                  ->orWhereHas('driver', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Order by created_at desc to show latest first and paginate
        $fuelRecords = $query->orderBy('created_at', 'desc')->paginate(30);

        // Calculate statistics from ALL filtered records (build same query for stats)
        $statsQuery = FuelRecord::query();
        
        if ($year && $month) {
            $statsQuery->whereYear('date', (int)$year)
                      ->whereMonth('date', (int)$month);
        } elseif ($monthCompound) {
            $date = \Carbon\Carbon::parse($monthCompound);
            $statsQuery->whereMonth('date', $date->month)
                      ->whereYear('date', $date->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($q) use ($search) {
                      $q->where('unit', 'like', "%{$search}%")
                        ->orWhere('plate', 'like', "%{$search}%");
                  })
                  ->orWhereHas('driver', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $allRecords = $statsQuery->get();
        $monthlyStats = [
            'total_cost' => $allRecords->sum('cost'),
            'total_liters' => $allRecords->sum('liters'),
            'total_km' => $allRecords->sum(function($record) {
                return $record->final_mileage - $record->initial_mileage;
            }),
            'avg_consumption' => $allRecords->count() > 0 
                ? round($allRecords->avg('km_per_liter'), 2)
                : 0,
        ];

        // Get vehicles with their current performance
        $vehicles = Vehicle::active()
            ->with('currentPerformance', 'fuelType')
            ->get();

        // Years available for filter (distinct years from records)
        $years = FuelRecord::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Get drivers, projects, and providers for the create modal
        $drivers = Driver::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $providers = FuelRecord::whereNotNull('provider_client')
            ->where('provider_client', '!=', '')
            ->distinct()
            ->pluck('provider_client')
            ->sort()
            ->values();

        return view('dashboard.index', compact('fuelRecords', 'monthlyStats', 'vehicles', 'years', 'drivers', 'projects', 'providers'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        $query = FuelRecord::with(['vehicle', 'driver', 'project']);

        if ($request->filled('month')) {
            $date = \Carbon\Carbon::parse($request->month);
            $query->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
        }

        return datatables()->eloquent($query)
            ->addColumn('vehicle_unit', function ($record) {
                return $record->vehicle->unit ?? '-';
            })
            ->addColumn('driver_name', function ($record) {
                return $record->driver->name ?? '-';
            })
            ->addColumn('project_name', function ($record) {
                return $record->project->name ?? '-';
            })
            ->make(true);
    }

    /**
     * Export fuel records to Excel.
     */
    public function export(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');
        $monthNumber = $request->query('month');
        // If year+month provided, build YYYY-MM for export
        if ($year && $monthNumber) {
            $month = sprintf('%04d-%02d', (int)$year, (int)$monthNumber);
        }
        $search = $request->query('search');

        // Build filename: CONS + MES (uppercase), include year for clarity
        $monthForName = $month ?? now()->format('Y-m');
        [$yy, $mm] = explode('-', $monthForName);
        $monthNames = [1 => 'ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
        $monthLabel = $monthNames[(int)$mm] ?? strtoupper(date('F', mktime(0,0,0,(int)$mm,1)));
        $filename = 'CONS ' . $monthLabel . ' ' . $yy . '.xlsx';

        return Excel::download(
            new FuelRecordsExport($month, $search),
            $filename
        );
    }
}

