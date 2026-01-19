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
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Refreso</th>
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
                            'refreso' => $record->refreso,
                        ];
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->refreso }}</td>
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
                            <button type="button" class="px-3 py-1 text-xs rounded bg-primary-600 text-white hover:bg-primary-700" data-record='@json($recordPayload)' data-update-url="{{ route('fuel-records.update', $record) }}" onclick="openEditModal(this)">Editar</button>
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
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Fecha</label>
                    <input type="date" name="date" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Fecha de regreso</label>
                    <input type="date" name="return_date" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Vehículo</label>
                    <select name="vehicle_id" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="">Selecciona</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->unit }} - {{ $vehicle->plate }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Conductor</label>
                    <select name="driver_id" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="">Selecciona</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proyecto</label>
                    <select name="project_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">N/A</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Turno (D/N)</label>
                    <select name="shift" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="day">Día</option>
                        <option value="night">Noche</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proveedor / Cliente</label>
                    <input type="text" name="provider_client" class="mt-1 w-full rounded border-gray-300" placeholder="Proveedor o cliente">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Destino</label>
                    <input type="text" name="destination" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Monto adicional</label>
                    <input type="number" step="0.01" name="amount" class="mt-1 w-full rounded border-gray-300" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Litros</label>
                    <input type="number" step="0.01" name="liters" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Precio por litro</label>
                    <input type="number" step="0.01" name="fuel_price" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje inicial</label>
                    <input type="number" name="initial_mileage" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje final</label>
                    <input type="number" name="final_mileage" class="mt-1 w-full rounded border-gray-300" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300" placeholder="Notas del movimiento"></textarea>
            </div>
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
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Fecha</label>
                    <input type="date" name="date" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Fecha de regreso</label>
                    <input type="date" name="return_date" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Vehículo</label>
                    <select name="vehicle_id" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="">Selecciona</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->unit }} - {{ $vehicle->plate }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Conductor</label>
                    <select name="driver_id" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="">Selecciona</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proyecto</label>
                    <select name="project_id" class="mt-1 w-full rounded border-gray-300">
                        <option value="">N/A</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Turno (D/N)</label>
                    <select name="shift" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="day">Día</option>
                        <option value="night">Noche</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Proveedor / Cliente</label>
                    <input type="text" name="provider_client" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Destino</label>
                    <input type="text" name="destination" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Monto adicional</label>
                    <input type="number" step="0.01" name="amount" class="mt-1 w-full rounded border-gray-300" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Litros</label>
                    <input type="number" step="0.01" name="liters" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Precio por litro</label>
                    <input type="number" step="0.01" name="fuel_price" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje inicial</label>
                    <input type="number" name="initial_mileage" class="mt-1 w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Kilometraje final</label>
                    <input type="number" name="final_mileage" class="mt-1 w-full rounded border-gray-300" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300"></textarea>
            </div>
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
        const form = document.getElementById('editForm');

        form.action = button.dataset.updateUrl;
        form.querySelector('input[name="date"]').value = record.date || '';
        form.querySelector('input[name="return_date"]').value = record.return_date || '';
        form.querySelector('select[name="vehicle_id"]').value = record.vehicle_id || '';
        form.querySelector('select[name="driver_id"]').value = record.driver_id || '';
        form.querySelector('select[name="project_id"]').value = record.project_id || '';
        form.querySelector('select[name="shift"]').value = record.shift || 'day';
        form.querySelector('input[name="provider_client"]').value = record.provider_client || '';
        form.querySelector('input[name="destination"]').value = record.destination || '';
        form.querySelector('input[name="amount"]').value = record.amount || '';
        form.querySelector('input[name="liters"]').value = record.liters || '';
        form.querySelector('input[name="fuel_price"]').value = record.fuel_price || '';
        form.querySelector('input[name="initial_mileage"]').value = record.initial_mileage || '';
        form.querySelector('input[name="final_mileage"]').value = record.final_mileage || '';
        form.querySelector('textarea[name="description"]').value = record.description || '';

        showModal('editModal');
    }
</script>
@endsection
