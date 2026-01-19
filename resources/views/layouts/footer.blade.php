<footer class="bg-gray-900 text-gray-100 border-t-4 border-primary-600 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-bold text-white mb-4">{{ config('app.name') }}</h3>
                <p class="text-gray-400 text-sm">
                    Sistema integral de gestión y control de combustible para optimizar el consumo y costos operacionales.
                </p>
            </div>

            <!-- Links -->
            <div>
                <h3 class="text-lg font-bold text-white mb-4">Enlaces</h3>
                <ul class="space-y-2 text-sm">
                    @auth
                    <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition">Dashboard</a></li>
                    <li><a href="{{ route('vehicles.index') }}" class="text-gray-400 hover:text-white transition">Vehículos</a></li>
                    @if(auth()->user()->hasRole('admin'))
                    <li><a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white transition">Usuarios</a></li>
                    @endif
                    @endauth
                </ul>
            </div>

            <!-- Info -->
            <div>
                <h3 class="text-lg font-bold text-white mb-4">Información</h3>
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} {{ config('app.name' )}}<br>
                    Todos los derechos reservados.
                </p>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
            <p>Desarrollado con <span class="text-primary-500">❤</span> para la optimización operacional</p>
        </div>
    </div>
</footer>
