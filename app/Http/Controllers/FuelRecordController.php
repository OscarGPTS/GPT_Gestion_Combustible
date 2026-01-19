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

        return view('fuel_records.index', compact('fuelRecords', 'vehicles', 'drivers', 'projects'));
    }

    /**
     * Store a new fuel record.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
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
            'driver_id' => ['required', 'exists:drivers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:date'],
            'shift' => ['required', 'in:day,night'],
            'provider_client' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'destination' => ['nullable', 'string', 'max:150'],
            'initial_mileage' => ['required', 'integer', 'min:0'],
            'final_mileage' => ['required', 'integer', 'gte:initial_mileage'],
            'liters' => ['required', 'numeric', 'min:0'],
            'fuel_price' => ['required', 'numeric', 'min:0'],
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
