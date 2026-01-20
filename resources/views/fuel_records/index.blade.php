@extends('layouts.app')

@section('title', 'Movimientos de combustible')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Movimientos de combustible</h1>
        <p class="text-sm text-gray-500">Crea, consulta y edita los registros con su evidencia.</p>
    </div>
    <button type="button" onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-primary-700 transition">
        + Nuevo registro
    </button>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Turno</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Vehículo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Conductor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Litros</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Costo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Destino</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Evidencia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($fuelRecords as $record)
                    @php
                        $evidenceUrls = collect($record->evidence ?? [])->map(fn($file) => asset('storage/evidence/' . $file));
                        $recordPayload = [
                            'id' => $record->id,
                            'folio' => $record->folio,
                            'date' => optional($record->date)->format('Y-m-d'),
                            'return_date' => optional($record->return_date)->format('Y-m-d'),
                            'shift' => $record->shift,
                            'np_text' => $record->np_text,
                            'provider_client' => $record->provider_client,
                            'description' => $record->description,
                            'destination' => $record->destination,
                            'initial_mileage' => $record->initial_mileage,
                            'final_mileage' => $record->final_mileage,
                            'liters' => $record->liters,
                            'fuel_price' => $record->fuel_price,
                            'cost' => $record->cost,
                            'amount' => $record->amount,
                            'vehicle_id' => $record->vehicle_id,
                            'vehicle' => $record->vehicle?->unit,
                            'driver_id' => $record->driver_id,
                            'driver' => $record->driver?->name,
                            'project_id' => $record->project_id,
                            'project' => $record->project?->name,
                            'gasoline_cost' => $record->gasoline_cost,
                            'diesel_cost' => $record->diesel_cost,
                            'refreso' => $record->refreso,
                        ];
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($record->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->shift === 'night' ? 'Noche' : 'Día' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->vehicle?->unit }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->driver?->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->liters }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">${{ number_format($record->cost, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->destination }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $evidenceUrls->count() }} img</td>
                        <td class="px-4 py-3 text-sm text-gray-700 space-x-2">
                            <button type="button" class="px-3 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200" data-record='@json($recordPayload)' data-evidence='@json($evidenceUrls)' onclick="openDetailModal(this)">Ver</button>
                            <button type="button" class="px-3 py-1 text-xs rounded bg-primary-600 text-white hover:bg-primary-700" data-record='@json($recordPayload)' data-evidence='@json($evidenceUrls)' data-update-url="{{ route('fuel-records.update', $record) }}" onclick="openEditModal(this)">Editar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">Sin registros aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $fuelRecords->links() }}</div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Nuevo movimiento</h2>
                <p class="text-sm text-gray-500">Captura la salida y agrega evidencia.</p>
            </div>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form id="createForm" action="{{ route('fuel-records.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 1. Unidad -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Unidad <span class="text-red-500">*</span></label>
                    <select name="vehicle_id" class="mt-1 w-full rounded border-gray-300 px-3 py-2" required>
                        <option value="" class="px-3 py-2">Selecciona</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->unit }} - {{ $vehicle->plate }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Fecha -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="date" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 4. Regreso -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Regreso</label>
                    <input type="date" name="return_date" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 5. Conductor -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Conductor <span class="text-red-500">*</span> <span class="text-green-600 text-xs">(crea nuevo si no existe)</span></label>
                    <input type="text" name="driver_name" list="drivers-list" class="mt-1 w-full rounded border-gray-300 focus:border-primary-500 focus:ring-primary-500 uppercase" placeholder="SELECCIONA O ESCRIBE NUEVO" required autocomplete="off" style="text-transform: uppercase;">
                    <datalist id="drivers-list">
                        @foreach($drivers as $driver)
                            <option value="{{ strtoupper($driver->name) }}" data-id="{{ $driver->id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" name="driver_id" value="">
                </div>

                <!-- 6. D/N (Día/Noche - texto libre) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">D/N</label>
                    <input type="text" name="shift" class="mt-1 w-full rounded border-gray-300" placeholder="Ingrese D/N" maxlength="50">
                </div>

                <!-- 7. N/P (texto libre) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">N/P</label>
                    <input type="text" name="np_text" class="mt-1 w-full rounded border-gray-300" placeholder="Ingrese N/P" maxlength="100">
                </div>

                <!-- 8. Proveedor o Cliente (datalist con creación) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proveedor o Cliente <span class="text-green-600 text-xs">(crea nuevo si no existe)</span></label>
                    <input type="text" name="provider_client" list="providers-list" class="mt-1 w-full rounded border-gray-300 uppercase" placeholder="SELECCIONA O ESCRIBE NUEVO" autocomplete="off" style="text-transform: uppercase;">
                    <datalist id="providers-list">
                        @foreach($providers as $provider)
                            <option value="{{ strtoupper($provider) }}">
                        @endforeach
                    </datalist>
                </div>

                <!-- 10. Destino -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Destino</label>
                    <input type="text" name="destination" class="mt-1 w-full rounded border-gray-300" placeholder="Lugar de destino">
                </div>

                <!-- 11. Kilometraje inicial -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje inicial <span class="text-red-500">*</span></label>
                    <input type="number" name="initial_mileage" id="initial_mileage_create" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 12. Kilometraje final -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje final <span class="text-red-500">*</span></label>
                    <input type="number" name="final_mileage" id="final_mileage_create" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 13. Kilómetros recorridos (calculado pero editable) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilómetros recorridos</label>
                    <input type="number" id="km_recorridos_create" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 14. Consumo (litros) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Consumo (litros) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="liters" id="liters_create" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 15. Costo (calculado pero editable) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Costo</label>
                    <input type="number" step="0.01" id="costo_create" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 16. Kilómetros por litro (calculado pero editable) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilómetros por litro</label>
                    <input type="number" step="0.01" id="km_per_liter_create" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 17. $ Gasolina -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Gasolina</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" step="0.01" name="gasoline_cost" id="gasoline_cost_create" class="w-full rounded border-gray-300 pl-7" placeholder="0.00">
                    </div>
                </div>

                <!-- 18. $ Diesel -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Diesel</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" step="0.01" name="diesel_cost" id="diesel_cost_create" class="w-full rounded border-gray-300 pl-7" placeholder="0.00">
                    </div>
                </div>

                <!-- Proyecto (opcional - mantener funcionalidad existente) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proyecto <span class="text-green-600 text-xs">(opcional - crea nuevo si no existe)</span></label>
                    <input type="text" name="project_name" list="projects-list" class="mt-1 w-full rounded border-gray-300 focus:border-primary-500 focus:ring-primary-500 uppercase" placeholder="SELECCIONA O ESCRIBE NUEVO" autocomplete="off" style="text-transform: uppercase;">
                    <datalist id="projects-list">
                        @foreach($projects as $project)
                            <option value="{{ strtoupper($project->name) }}" data-id="{{ $project->id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" name="project_id" value="">
                </div>
            </div>

            <!-- 9. Descripción (ancho completo) -->
            <div>
                <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300 px-3 py-2" placeholder="Notas del movimiento"></textarea>
            </div>

            <!-- Evidencia -->
            <div>
                <label class="block text-xs font-semibold text-gray-600">Evidencia (imágenes)</label>
                <input type="file" name="evidence[]" accept="image/*" multiple class="mt-1 w-full rounded border-gray-300">
                <p class="text-xs text-gray-500 mt-1">Se guardan en storage con nombres UUID para evitar duplicados.</p>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" class="px-4 py-2 text-sm text-gray-600" onclick="closeModal('createModal')">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Editar movimiento</h2>
                <p class="text-sm text-gray-500">Agrega nueva evidencia si lo necesitas (no se reemplaza la existente).</p>
            </div>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 1. Folio (readonly) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Folio</label>
                    <input type="text" id="edit_folio" class="mt-1 w-full rounded border-gray-300 bg-gray-50" readonly>
                </div>

                <!-- 2. Unidad -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Unidad <span class="text-red-500">*</span></label>
                    <select name="vehicle_id" class="mt-1 w-full rounded border-gray-300 px-3 py-2" required>
                        <option value="">Selecciona</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->unit }} - {{ $vehicle->plate }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Fecha -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="date" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 4. Regreso -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Regreso</label>
                    <input type="date" name="return_date" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 5. Conductor -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Conductor <span class="text-red-500">*</span> <span class="text-green-600 text-xs">(crea nuevo si no existe)</span></label>
                    <input type="text" name="driver_name_edit" list="drivers-list-edit" class="mt-1 w-full rounded border-gray-300 focus:border-primary-500 focus:ring-primary-500 uppercase" placeholder="SELECCIONA O ESCRIBE NUEVO" required autocomplete="off" style="text-transform: uppercase;">
                    <datalist id="drivers-list-edit">
                        @foreach($drivers as $driver)
                            <option value="{{ strtoupper($driver->name) }}" data-id="{{ $driver->id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" name="driver_id" value="">
                </div>

                <!-- 6. D/N (texto libre) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">D/N</label>
                    <input type="text" name="shift" class="mt-1 w-full rounded border-gray-300" placeholder="Día, Noche, etc." maxlength="50">
                </div>

                <!-- 7. N/P (texto libre) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">N/P</label>
                    <input type="text" name="np_text" class="mt-1 w-full rounded border-gray-300" placeholder="Número de parte, etc." maxlength="100">
                </div>

                <!-- 8. Proveedor o Cliente (datalist con creación) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proveedor o Cliente</label>
                    <input type="text" name="provider_client" list="providers-list-edit" class="mt-1 w-full rounded border-gray-300 uppercase" placeholder="SELECCIONA O ESCRIBE NUEVO" autocomplete="off" style="text-transform: uppercase;">
                    <datalist id="providers-list-edit">
                        @foreach($providers as $provider)
                            <option value="{{ strtoupper($provider) }}">
                        @endforeach
                    </datalist>
                </div>

                <!-- 10. Destino -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Destino</label>
                    <input type="text" name="destination" class="mt-1 w-full rounded border-gray-300" placeholder="Lugar de destino">
                </div>

                <!-- 11. Kilometraje inicial -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje inicial <span class="text-red-500">*</span></label>
                    <input type="number" name="initial_mileage" id="initial_mileage_edit" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 12. Kilometraje final -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje final <span class="text-red-500">*</span></label>
                    <input type="number" name="final_mileage" id="final_mileage_edit" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 13. Kilómetros recorridos (calculado pero editable) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilómetros recorridos</label>
                    <input type="number" id="km_recorridos_edit" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 14. Consumo (litros) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Consumo (litros) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="liters" id="liters_edit" class="mt-1 w-full rounded border-gray-300" required>
                </div>

                <!-- 15. Costo (calculado pero editable) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Costo</label>
                    <input type="number" step="0.01" id="costo_edit" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 16. Kilómetros por litro (calculado pero editable) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilómetros por litro</label>
                    <input type="number" step="0.01" id="km_per_liter_edit" class="mt-1 w-full rounded border-gray-300">
                </div>

                <!-- 17. $ Gasolina -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Gasolina</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" step="0.01" name="gasoline_cost" id="gasoline_cost_edit" class="w-full rounded border-gray-300 pl-7" placeholder="0.00">
                    </div>
                </div>

                <!-- 18. $ Diesel -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Diesel</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" step="0.01" name="diesel_cost" id="diesel_cost_edit" class="w-full rounded border-gray-300 pl-7" placeholder="0.00">
                    </div>
                </div>

                <!-- Proyecto (opcional) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proyecto <span class="text-green-600 text-xs">(opcional - crea nuevo si no existe)</span></label>
                    <input type="text" name="project_name_edit" list="projects-list-edit" class="mt-1 w-full rounded border-gray-300 focus:border-primary-500 focus:ring-primary-500 uppercase" placeholder="SELECCIONA O ESCRIBE NUEVO" autocomplete="off" style="text-transform: uppercase;">
                    <datalist id="projects-list-edit">
                        @foreach($projects as $project)
                            <option value="{{ strtoupper($project->name) }}" data-id="{{ $project->id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" name="project_id" value="">
                </div>
            </div>

            <!-- 9. Descripción (ancho completo) -->
            <div>
                <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300 px-3 py-2" placeholder="Notas del movimiento"></textarea>
            </div>

            <!-- Evidencia existente -->
            <div>
                <label class="block text-xs font-semibold text-gray-600">Evidencia existente</label>
                <div id="editFormEvidence" class="mt-1 space-y-1"></div>
            </div>

            <!-- Agregar más evidencia -->
            <div>
                <label class="block text-xs font-semibold text-gray-600">Agregar más evidencia (opcional)</label>
                <input type="file" name="evidence[]" accept="image/*" multiple class="mt-1 w-full rounded border-gray-300">
                <p class="text-xs text-gray-500 mt-1">Las imágenes se guardan con UUID dentro de storage/evidence.</p>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" class="px-4 py-2 text-sm text-gray-600" onclick="closeModal('editModal')">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 id="detailTitle" class="text-lg font-semibold text-gray-900">Detalle</h2>
                <p class="text-sm text-gray-500">Incluye datos generales y evidencia.</p>
            </div>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <p><span class="font-semibold text-gray-700">Vehículo:</span> <span id="detailVehicle" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Conductor:</span> <span id="detailDriver" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Proyecto:</span> <span id="detailProject" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Fecha:</span> <span id="detailDate" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Regreso:</span> <span id="detailReturnDate" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Turno (D/N):</span> <span id="detailShift" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Destino:</span> <span id="detailDestination" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Proveedor/Cliente:</span> <span id="detailProvider" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Litros:</span> <span id="detailLiters" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Precio/L:</span> <span id="detailPrice" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Costo:</span> <span id="detailCost" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Monto adicional:</span> <span id="detailAmount" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Km inicial:</span> <span id="detailInitialMileage" class="text-gray-800"></span></p>
                <p><span class="font-semibold text-gray-700">Km final:</span> <span id="detailFinalMileage" class="text-gray-800"></span></p>
                <p class="md:col-span-2"><span class="font-semibold text-gray-700">Descripción:</span> <span id="detailDescription" class="text-gray-800"></span></p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Evidencia</p>
                <div id="detailEvidence" class="space-y-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function openCreateModal() {
        document.getElementById('createForm').reset();
        showModal('createModal');
    }

    function openDetailModal(button) {
        const record = JSON.parse(button.dataset.record || '{}');
        const evidence = JSON.parse(button.dataset.evidence || '[]');

        document.getElementById('detailTitle').textContent = `Folio ${record.folio || ''}`;
        document.getElementById('detailVehicle').textContent = record.vehicle || '';
        document.getElementById('detailDriver').textContent = record.driver || '';
        document.getElementById('detailProject').textContent = record.project || 'N/A';
        document.getElementById('detailDate').textContent = record.date || '';
        document.getElementById('detailReturnDate').textContent = record.return_date || 'N/A';
        document.getElementById('detailShift').textContent = record.shift === 'night' ? 'Noche' : 'Día';
        document.getElementById('detailDestination').textContent = record.destination || '';
        document.getElementById('detailProvider').textContent = record.provider_client || '';
        document.getElementById('detailLiters').textContent = record.liters || '';
        document.getElementById('detailPrice').textContent = record.fuel_price || '';
        document.getElementById('detailCost').textContent = record.cost || '';
        document.getElementById('detailAmount').textContent = record.amount || '0';
        document.getElementById('detailInitialMileage').textContent = record.initial_mileage || '';
        document.getElementById('detailFinalMileage').textContent = record.final_mileage || '';
        document.getElementById('detailDescription').textContent = record.description || '';

        const evidenceContainer = document.getElementById('detailEvidence');
        evidenceContainer.innerHTML = '';

        if (evidence.length === 0) {
            evidenceContainer.innerHTML = '<p class="text-sm text-gray-500">Sin evidencia adjunta.</p>';
        } else {
            evidence.forEach((url, index) => {
                const link = document.createElement('a');
                link.href = url;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.className = 'block text-primary-700 hover:underline';
                link.textContent = `Evidencia ${index + 1}`;
                evidenceContainer.appendChild(link);
            });
        }

        showModal('detailModal');
    }

    function openEditModal(button) {
        const record = JSON.parse(button.dataset.record || '{}');
        const evidence = JSON.parse(button.dataset.evidence || '[]');
        const form = document.getElementById('editForm');

        form.action = button.dataset.updateUrl;
        
        // Poblar todos los campos
        document.getElementById('edit_folio').value = record.folio || '';
        form.querySelector('input[name="date"]').value = record.date || '';
        form.querySelector('input[name="return_date"]').value = record.return_date || '';
        form.querySelector('select[name="vehicle_id"]').value = record.vehicle_id || '';
        form.querySelector('input[name="driver_name_edit"]').value = record.driver || '';
        form.querySelector('input[name="driver_id"]').value = record.driver_id || '';
        form.querySelector('input[name="shift"]').value = record.shift || '';
        form.querySelector('input[name="np_text"]').value = record.np_text || '';
        form.querySelector('input[name="provider_client"]').value = record.provider_client ? record.provider_client.toUpperCase() : '';
        form.querySelector('input[name="destination"]').value = record.destination || '';
        form.querySelector('input[name="project_name_edit"]').value = record.project || '';
        form.querySelector('input[name="project_id"]').value = record.project_id || '';
        
        // Campos numéricos y calculables
        document.getElementById('initial_mileage_edit').value = record.initial_mileage || '';
        document.getElementById('final_mileage_edit').value = record.final_mileage || '';
        document.getElementById('liters_edit').value = record.liters || '';
        document.getElementById('gasoline_cost_edit').value = record.gasoline_cost || '';
        document.getElementById('diesel_cost_edit').value = record.diesel_cost || '';
        
        form.querySelector('textarea[name="description"]').value = record.description || '';

        // Mostrar evidencia existente
        const evidenceContainer = document.getElementById('editFormEvidence');
        if (evidenceContainer) {
            evidenceContainer.innerHTML = '';
            if (evidence.length === 0) {
                evidenceContainer.innerHTML = '<p class="text-xs text-gray-500">Sin evidencia adjunta.</p>';
            } else {
                evidence.forEach((url, index) => {
                    const link = document.createElement('a');
                    link.href = url;
                    link.target = '_blank';
                    link.rel = 'noopener noreferrer';
                    link.className = 'block text-primary-700 hover:underline text-xs';
                    link.textContent = `Evidencia ${index + 1}`;
                    evidenceContainer.appendChild(link);
                });
            }
        }

        // Recalcular campos automáticos
        calcularCamposEdit();

        showModal('editModal');
    }

    // Autocomplete para conductores y proyectos
    document.addEventListener('DOMContentLoaded', function() {
        // Formulario de crear
        const driverInput = document.querySelector('input[name="driver_name"]');
        const driverIdInput = document.querySelector('#createForm input[name="driver_id"]');
        const projectInput = document.querySelector('input[name="project_name"]');
        const projectIdInput = document.querySelector('#createForm input[name="project_id"]');
        const providerInput = document.querySelector('input[name="provider_client"]');
        
        if (driverInput) {
            driverInput.addEventListener('input', function() {
                                // Convertir a mayúsculas en tiempo real
                                this.value = this.value.toUpperCase();
                
                const options = document.querySelectorAll('#drivers-list option');
                let found = false;
                options.forEach(option => {
                    if (option.value.toUpperCase() === this.value.toUpperCase()) {
                        driverIdInput.value = option.getAttribute('data-id');
                        found = true;
                    }
                });
                if (!found) {
                    driverIdInput.value = '';
                }
            });
        }

        if (projectInput) {
            projectInput.addEventListener('input', function() {
                                // Convertir a mayúsculas en tiempo real
                                this.value = this.value.toUpperCase();
                
                const options = document.querySelectorAll('#projects-list option');
                let found = false;
                options.forEach(option => {
                    if (option.value.toUpperCase() === this.value.toUpperCase()) {
                        projectIdInput.value = option.getAttribute('data-id');
                        found = true;
                    }
                });
                if (!found) {
                    projectIdInput.value = '';
                }
            });
        }

        if (providerInput) {
            providerInput.addEventListener('input', function() {
                                this.value = this.value.toUpperCase();
            });
        }

        // Formulario de editar
        const driverInputEdit = document.querySelector('input[name="driver_name_edit"]');
        const driverIdInputEdit = document.querySelector('#editForm input[name="driver_id"]');
        const projectInputEdit = document.querySelector('input[name="project_name_edit"]');
        const projectIdInputEdit = document.querySelector('#editForm input[name="project_id"]');
        const providerInputEdit = document.querySelector('#editForm input[name="provider_client"]');
        
        if (driverInputEdit) {
            driverInputEdit.addEventListener('input', function() {
                                // Convertir a mayúsculas en tiempo real
                                this.value = this.value.toUpperCase();
                
                const options = document.querySelectorAll('#drivers-list-edit option');
                let found = false;
                options.forEach(option => {
                    if (option.value.toUpperCase() === this.value.toUpperCase()) {
                        driverIdInputEdit.value = option.getAttribute('data-id');
                        found = true;
                    }
                });
                if (!found) {
                    driverIdInputEdit.value = '';
                }
            });
        }

        if (projectInputEdit) {
            projectInputEdit.addEventListener('input', function() {
                                // Convertir a mayúsculas en tiempo real
                                this.value = this.value.toUpperCase();
                
                const options = document.querySelectorAll('#projects-list-edit option');
                let found = false;
                options.forEach(option => {
                    if (option.value.toUpperCase() === this.value.toUpperCase()) {
                        projectIdInputEdit.value = option.getAttribute('data-id');
                        found = true;
                    }
                });
                if (!found) {
                    projectIdInputEdit.value = '';
                }
            });
        }

        if (providerInputEdit) {
            providerInputEdit.addEventListener('input', function() {
                                this.value = this.value.toUpperCase();
            });
        }

        // Cálculos automáticos para formulario CREATE
        function calcularCamposCreate() {
            const inicialEl = document.getElementById('initial_mileage_create');
            const finalEl = document.getElementById('final_mileage_create');
            const litrosEl = document.getElementById('liters_create');
            const gasolinaEl = document.getElementById('gasoline_cost_create');
            const dieselEl = document.getElementById('diesel_cost_create');
            const kmRecorridosEl = document.getElementById('km_recorridos_create');
            const costoEl = document.getElementById('costo_create');
            const kmPorLitroEl = document.getElementById('km_per_liter_create');

            const inicial = parseFloat(inicialEl.value) || 0;
            const final = parseFloat(finalEl.value) || 0;
            const litros = parseFloat(litrosEl.value) || 0;
            const gasolina = parseFloat(gasolinaEl.value) || 0;
            const diesel = parseFloat(dieselEl.value) || 0;

            // Kilómetros recorridos = final - inicial
            const kmRecorridos = final - inicial;
            kmRecorridosEl.value = kmRecorridos > 0 ? kmRecorridos.toFixed(2) : '';

            // Costo total = gasolina + diesel
            const costo = gasolina + diesel;
            costoEl.value = costo > 0 ? costo.toFixed(2) : '';

            // Kilómetros por litro = km recorridos / litros
            if (litros > 0 && kmRecorridos > 0) {
                const kmPorLitro = kmRecorridos / litros;
                kmPorLitroEl.value = kmPorLitro.toFixed(2);
            } else {
                kmPorLitroEl.value = '';
            }
        }

        // Agregar listeners a todos los campos que afectan los cálculos
        ['initial_mileage_create', 'final_mileage_create', 'liters_create', 'gasoline_cost_create', 'diesel_cost_create'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', calcularCamposCreate);
            }
        });

        // Cálculos automáticos para formulario EDIT
        function calcularCamposEdit() {
            const inicialEl = document.getElementById('initial_mileage_edit');
            const finalEl = document.getElementById('final_mileage_edit');
            const litrosEl = document.getElementById('liters_edit');
            const gasolinaEl = document.getElementById('gasoline_cost_edit');
            const dieselEl = document.getElementById('diesel_cost_edit');
            const kmRecorridosEl = document.getElementById('km_recorridos_edit');
            const costoEl = document.getElementById('costo_edit');
            const kmPorLitroEl = document.getElementById('km_per_liter_edit');

            const inicial = parseFloat(inicialEl.value) || 0;
            const final = parseFloat(finalEl.value) || 0;
            const litros = parseFloat(litrosEl.value) || 0;
            const gasolina = parseFloat(gasolinaEl.value) || 0;
            const diesel = parseFloat(dieselEl.value) || 0;

            // Kilómetros recorridos = final - inicial
            const kmRecorridos = final - inicial;
            kmRecorridosEl.value = kmRecorridos > 0 ? kmRecorridos.toFixed(2) : '';

            // Costo total = gasolina + diesel
            const costo = gasolina + diesel;
            costoEl.value = costo > 0 ? costo.toFixed(2) : '';

            // Kilómetros por litro = km recorridos / litros
            if (litros > 0 && kmRecorridos > 0) {
                const kmPorLitro = kmRecorridos / litros;
                kmPorLitroEl.value = kmPorLitro.toFixed(2);
            } else {
                kmPorLitroEl.value = '';
            }
        }

        // Agregar listeners al formulario de edición
        ['initial_mileage_edit', 'final_mileage_edit', 'liters_edit', 'gasoline_cost_edit', 'diesel_cost_edit'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', calcularCamposEdit);
            }
        });

        // Hacer calcularCamposEdit global para que openEditModal pueda usarla
        window.calcularCamposEdit = calcularCamposEdit;
    });
</script>
@endsection
