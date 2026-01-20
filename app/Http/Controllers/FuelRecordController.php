<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\FuelRecord;
use App\Models\Project;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FuelRecordController extends Controller
{
    /**
     * Show the list of fuel movements.
     */
    public function index()
    {
        $fuelRecords = FuelRecord::with(['vehicle', 'driver', 'project'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);

        $vehicles = Vehicle::active()->orderBy('unit')->get();
        $drivers = Driver::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();
        $providers = FuelRecord::whereNotNull('provider_client')
            ->where('provider_client', '<>', '')
            ->select('provider_client')
            ->distinct()
            ->orderBy('provider_client')
            ->pluck('provider_client');

        return view('fuel_records.index', compact('fuelRecords', 'vehicles', 'drivers', 'projects', 'providers'));
    }

    /**
     * Store a new fuel record.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        
        // Auto-create driver if driver_id is empty but driver_name is provided
        if (empty($data['driver_id']) && $request->filled('driver_name')) {
            $driverName = mb_strtoupper(trim($request->driver_name));
            // Buscar case-insensitive para evitar duplicados
            $driver = Driver::whereRaw('UPPER(name) = ?', [$driverName])->first();
            if (!$driver) {
                $driver = Driver::create([
                    'name' => $driverName,
                    'status' => 'active'
                ]);
            }
            $data['driver_id'] = $driver->id;
        }
        
        // Auto-create project if project_id is empty but project_name is provided
        if (empty($data['project_id']) && $request->filled('project_name')) {
            $projectName = mb_strtoupper(trim($request->project_name));
            // Buscar case-insensitive para evitar duplicados
            $project = Project::whereRaw('UPPER(name) = ?', [$projectName])->first();
            if (!$project) {
                $project = Project::create([
                    'name' => $projectName,
                    'status' => 'active'
                ]);
            }
            $data['project_id'] = $project->id;
        }

        // Normalizar proveedor/cliente a mayúsculas para evitar duplicados por casing
        $data['provider_client'] = $request->filled('provider_client')
            ? mb_strtoupper(trim($request->provider_client))
            : null;
        
        $data['cost'] = $this->calculateCost($data['liters'], $data['fuel_price']);
        $data['evidence'] = $this->storeEvidence($request);

        $record = FuelRecord::create($data);
        $record->update(['folio' => 'F-' . str_pad((string) $record->id, 6, '0', STR_PAD_LEFT)]);

        return redirect()->route('fuel-records.index')->with('success', 'Registro creado correctamente.');
    }

    /**
     * Return a single record as JSON for modal display.
     */
    public function show(FuelRecord $fuelRecord)
    {
        $fuelRecord->load(['vehicle', 'driver', 'project']);

        return response()->json([
            'record' => $fuelRecord,
            'evidence_urls' => collect($fuelRecord->evidence ?? [])->map(fn ($file) => asset('storage/evidence/' . $file)),
        ]);
    }

    /**
     * Update an existing fuel record.
     */
    public function update(Request $request, FuelRecord $fuelRecord)
    {
        $data = $this->validateData($request, $fuelRecord->id);
        
        // Auto-create driver if driver_id is empty but driver_name is provided
        if (empty($data['driver_id']) && $request->filled('driver_name_edit')) {
            $driverName = mb_strtoupper(trim($request->driver_name_edit));
            // Buscar case-insensitive para evitar duplicados
            $driver = Driver::whereRaw('UPPER(name) = ?', [$driverName])->first();
            if (!$driver) {
                $driver = Driver::create([
                    'name' => $driverName,
                    'status' => 'active'
                ]);
            }
            $data['driver_id'] = $driver->id;
        }
        
        // Auto-create project if project_id is empty but project_name is provided
        if (empty($data['project_id']) && $request->filled('project_name_edit')) {
            $projectName = mb_strtoupper(trim($request->project_name_edit));
            // Buscar case-insensitive para evitar duplicados
            $project = Project::whereRaw('UPPER(name) = ?', [$projectName])->first();
            if (!$project) {
                $project = Project::create([
                    'name' => $projectName,
                    'status' => 'active'
                ]);
            }
            $data['project_id'] = $project->id;
        }

        // Normalizar proveedor/cliente a mayúsculas para evitar duplicados por casing
        $data['provider_client'] = $request->filled('provider_client')
            ? mb_strtoupper(trim($request->provider_client))
            : null;
        
        $data['cost'] = $this->calculateCost($data['liters'], $data['fuel_price']);
        $data['evidence'] = $this->storeEvidence($request, $fuelRecord->evidence ?? []);

        $fuelRecord->update($data);

        return redirect()->route('fuel-records.index')->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Delete a fuel record.
     */
    public function destroy(FuelRecord $fuelRecord)
    {
        $fuelRecord->delete();

        return redirect()->route('fuel-records.index')->with('success', 'Registro eliminado.');
    }

    /**
     * Validate incoming data.
     */
    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'driver_name_edit' => ['nullable', 'string', 'max:100'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_name' => ['nullable', 'string', 'max:120'],
            'project_name_edit' => ['nullable', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:date'],
            'shift' => ['nullable', 'string', 'max:50'], // D/N campo texto
            'np_text' => ['nullable', 'string', 'max:100'], // N/P campo texto
            'provider_client' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'destination' => ['nullable', 'string', 'max:150'],
            'initial_mileage' => ['required', 'integer', 'min:0'],
            'final_mileage' => ['required', 'integer', 'gte:initial_mileage'],
            'liters' => ['required', 'numeric', 'min:0'], // Consumo
            'fuel_price' => ['nullable', 'numeric', 'min:0'],
            'gasoline_cost' => ['nullable', 'numeric', 'min:0'], // $ Gasolina
            'diesel_cost' => ['nullable', 'numeric', 'min:0'], // $ Diesel
            'amount' => ['nullable', 'numeric', 'min:0'],
            'evidence.*' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    /**
     * Build the next folio in format F-000001.
     */
    private function nextFolio(): string
    {
        $latest = FuelRecord::orderByDesc('id')->value('folio');
        $number = 0;

        return 'F-' . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Persist evidence images and return filenames.
     */
    private function storeEvidence(Request $request, array $existing = []): array
    {
        $evidence = $existing;

        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('evidence', $filename, 'public');
                $evidence[] = $filename;
            }
        }

        return $evidence;
    }

    private function calculateCost(float $liters, float $fuelPrice): float
    {
        return round($liters * $fuelPrice, 2);
    }
}
