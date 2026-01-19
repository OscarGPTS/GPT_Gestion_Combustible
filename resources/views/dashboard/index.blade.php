@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Reporte general de movimientos de combustible</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col sm:flex-row gap-3">
            <input type="month" id="monthFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" value="{{ date('Y-m') }}">
            <input type="text" id="searchFilter" placeholder="Buscar..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button id="exportBtn" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Excel
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-primary-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Costo Total del Mes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        ${{ number_format($monthlyStats['total_cost'], 2) }}
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-accent-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Litros Consumidos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($monthlyStats['total_liters'], 2) }} L
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-accent-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">KM Recorridos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($monthlyStats['total_km'], 0) }} km
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Consumo Promedio</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($monthlyStats['avg_consumption'] ?? 0, 2) }} km/L
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="border-b border-gray-200">
            <div class="flex" role="tablist">
                <button role="tab" aria-selected="true" aria-controls="panel-records" 
                        class="tab-btn px-6 py-4 font-medium text-primary-600 border-b-2 border-primary-600 focus:outline-none transition"
                        data-tab="records">
                    Reporte Mensual General
                </button>
                <button role="tab" aria-selected="false" aria-controls="panel-performance" 
                        class="tab-btn px-6 py-4 font-medium text-gray-600 border-b-2 border-transparent hover:border-gray-300 focus:outline-none transition"
                        data-tab="performance">
                    Rendimiento por Vehículo
                </button>
                <button role="tab" aria-selected="false" aria-controls="panel-vehicles" 
                        class="tab-btn px-6 py-4 font-medium text-gray-600 border-b-2 border-transparent hover:border-gray-300 focus:outline-none transition"
                        data-tab="vehicles">
                    Vehículos
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Reporte Mensual General -->
            <div id="panel-records" role="tabpanel" class="">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="recordsTable">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Folio</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Unidad</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Fecha</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Conductor</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">KM Recorridos</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Litros</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Costo</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">KM/L</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fuelRecords as $record)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $record->folio }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $record->vehicle->unit }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $record->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $record->driver->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $record->mileage_traveled }} km</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($record->liters, 2) }} L</td>
                                <td class="px-4 py-3 font-medium text-primary-600">${{ number_format($record->cost, 2) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($record->km_per_liter, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    No hay registros disponibles
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rendimiento por Vehículo -->
            <div id="panel-performance" role="tabpanel" class="hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Unidad</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Tipo de Combustible</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Precio</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Rendimiento (KM/L)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-900">Fecha Vigencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $vehicle->unit }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $vehicle->fuelType->display_name }}</td>
                                <td class="px-4 py-3 font-medium text-primary-600">
                                    ${{ number_format($vehicle->currentPerformance?->fuel_price ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ number_format($vehicle->currentPerformance?->performance ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $vehicle->currentPerformance?->effective_date?->format('d/m/Y') ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    No hay vehículos registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vehículos -->
            <div id="panel-vehicles" role="tabpanel" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($vehicles as $vehicle)
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $vehicle->unit }}</h3>
                                <p class="text-sm text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            </div>
                            <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                                {{ $vehicle->fuelType->display_name }}
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-sm mb-4">
                            <p class="text-gray-600"><span class="font-medium">Placa:</span> {{ $vehicle->plate }}</p>
                            <p class="text-gray-600"><span class="font-medium">Año:</span> {{ $vehicle->year ?? '-' }}</p>
                            <p class="text-gray-600"><span class="font-medium">Tanque:</span> {{ $vehicle->tank_capacity ?? '-' }} L</p>
                            <p class="text-gray-600"><span class="font-medium">Consumo Promedio:</span> {{ number_format($vehicle->average_consumption ?? 0, 2) }} km/L</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-xs text-gray-500">
                                <span class="font-medium">Costo Acumulado:</span> ${{ number_format($vehicle->total_cost ?? 0, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="font-medium">KM Recorridos:</span> {{ number_format($vehicle->total_mileage ?? 0, 0) }} km
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-600">No hay vehículos registrados</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        document.querySelectorAll('[role="tabpanel"]').forEach(panel => {
            panel.classList.add('hidden');
        });
        
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-primary-600', 'text-primary-600');
            b.classList.add('border-transparent', 'text-gray-600');
        });
        
        document.getElementById(`panel-${tabName}`).classList.remove('hidden');
        this.classList.remove('border-transparent', 'text-gray-600');
        this.classList.add('border-primary-600', 'text-primary-600');
    });
});

// Filter functionality
document.getElementById('monthFilter').addEventListener('change', function() {
    location.href = `?month=${this.value}`;
});

document.getElementById('searchFilter').addEventListener('change', function() {
    if(this.value.trim()) {
        location.href = `?search=${encodeURIComponent(this.value)}`;
    }
});

// Export functionality
document.getElementById('exportBtn').addEventListener('click', function() {
    const month = document.getElementById('monthFilter').value;
    const search = document.getElementById('searchFilter').value;
    
    let url = '{{ route("dashboard.export") }}';
    const params = new URLSearchParams();
    
    if (month) params.append('month', month);
    if (search) params.append('search', search);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
});
</script>
@endsection
