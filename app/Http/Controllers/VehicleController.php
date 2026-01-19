<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::with('fuelType')->orderBy('unit')->get();
        $fuelTypes = FuelType::all();
        return view('vehicles.index', compact('vehicles', 'fuelTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'plate' => 'required|string|max:20|unique:vehicles,plate',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'tank_capacity' => 'nullable|numeric|min:0',
            'initial_mileage' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['active'] = true;

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehículo creado exitosamente.',
            'vehicle' => $vehicle->load('fuelType'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['fuelType', 'fuelRecords.driver', 'currentPerformance']);
        
        return response()->json([
            'success' => true,
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'unit' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'plate' => 'required|string|max:20|unique:vehicles,plate,' . $vehicle->id,
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'tank_capacity' => 'nullable|numeric|min:0',
            'initial_mileage' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehículo actualizado exitosamente.',
            'vehicle' => $vehicle->load('fuelType'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Soft delete by setting active to false
        $vehicle->update(['active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Vehículo eliminado exitosamente.',
        ]);
    }
}
