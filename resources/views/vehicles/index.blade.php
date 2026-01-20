@extends('layouts.app')

@section('title', 'Vehículos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Vehículos</h1>
            <p class="mt-2 text-gray-600">Gestión de unidades de transporte</p>
        </div>
        <button onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-primary-700 transition">
            <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Vehículo
        </button>
    </div>

    <!-- Vehicles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="vehiclesGrid">
        @forelse($vehicles as $vehicle)
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition border border-gray-100">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $vehicle->unit }}</h3>
                        <p class="text-sm text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                    </div>
                    <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                        {{ $vehicle->fuelType->display_name }}
                    </span>
                </div>

                <div class="space-y-2 text-sm mb-6 pb-6 border-b border-gray-200">
                    <p class="text-gray-600"><span class="font-medium">Placa:</span> {{ $vehicle->plate }}</p>
                    <p class="text-gray-600"><span class="font-medium">Año:</span> {{ $vehicle->year ?? '-' }}</p>
                    <p class="text-gray-600"><span class="font-medium">Tanque:</span> {{ $vehicle->tank_capacity ?? '-' }} L</p>
                </div>

                <div class="flex gap-2">
                    <button onclick="editVehicle({{ $vehicle->id }})" class="flex-1 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                        <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                    <button onclick="deleteVehicle({{ $vehicle->id }})" class="flex-1 px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition text-sm font-medium">
                        <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="mt-4 text-gray-600">No hay vehículos registrados</p>
            <button onclick="openModal()" class="mt-4 inline-block px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Crear primer vehículo
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal -->
<div id="vehicleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Nuevo Vehículo</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="vehicleForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidad *</label>
                <input type="text" name="unit" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                <input type="text" name="brand" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                <input type="text" name="model" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                    <input type="number" name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                <input type="text" name="plate" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Combustible *</label>
                    <select name="fuel_type_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Seleccionar...</option>
                        @foreach($fuelTypes as $fuelType)
                        <option value="{{ $fuelType->id }}">{{ $fuelType->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad Tanque (L)</label>
                    <input type="number" name="tank_capacity" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:shadow-lg transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentVehicleId = null;

function openModal() {
    currentVehicleId = null;
    document.getElementById('modalTitle').textContent = 'Nuevo Vehículo';
    document.getElementById('vehicleForm').reset();
    document.getElementById('vehicleModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('vehicleModal').classList.add('hidden');
}

function editVehicle(id) {
    currentVehicleId = id;
    fetch(`/vehicles/${id}`)
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                const form = document.getElementById('vehicleForm');
                form.unit.value = data.vehicle.unit;
                form.brand.value = data.vehicle.brand;
                form.model.value = data.vehicle.model;
                form.year.value = data.vehicle.year || '';
                form.plate.value = data.vehicle.plate;
                form.fuel_type_id.value = data.vehicle.fuel_type_id;
                form.tank_capacity.value = data.vehicle.tank_capacity || '';
                form.color.value = data.vehicle.color || '';
                form.notes.value = data.vehicle.notes || '';
                
                document.getElementById('modalTitle').textContent = 'Editar Vehículo';
                document.getElementById('vehicleModal').classList.remove('hidden');
            }
        });
}

function deleteVehicle(id) {
    if(confirm('¿Estás seguro de que deseas eliminar este vehículo?')) {
        fetch(`/vehicles/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    }
}

document.getElementById('vehicleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const method = currentVehicleId ? 'PUT' : 'POST';
    const url = currentVehicleId ? `/vehicles/${currentVehicleId}` : '/vehicles';
    
    const formData = new FormData(this);
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});

document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') closeModal();
});
</script>
@endsection
