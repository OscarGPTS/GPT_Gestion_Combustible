@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Usuarios</h1>
            <p class="mt-2 text-gray-600">Gestión de accesos y permisos del sistema</p>
        </div>
        <button onclick="openModal()" class="mt-4 md:mt-0 px-6 py-2 bg-gradient-to-r from-primary-600 to-accent-500 text-white rounded-lg hover:shadow-lg transition font-medium">
            <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Nombre</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Rol</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->roles->count())
                                <span class="inline-block px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                                    {{ $user->roles->first()->display_name }}
                                </span>
                            @else
                                <span class="text-gray-500 text-sm">Sin rol</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button onclick="editUser({{ $user->id }})" class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                                    Editar
                                </button>
                                @if(auth()->id() !== $user->id)
                                <button onclick="deleteUser({{ $user->id }})" class="px-3 py-1 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition text-sm font-medium">
                                    Eliminar
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No hay usuarios registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Nuevo Usuario</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="userForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña <span class="text-xs text-gray-500">(dejar en blanco para no cambiar)</span></label>
                <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                <select name="role_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Seleccionar rol...</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-primary-600 to-accent-500 text-white rounded-lg font-medium hover:shadow-lg transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentUserId = null;

function openModal() {
    currentUserId = null;
    document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
    document.getElementById('userForm').reset();
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function editUser(id) {
    currentUserId = id;
    fetch(`/users/${id}`)
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                const form = document.getElementById('userForm');
                form.name.value = data.user.name;
                form.email.value = data.user.email;
                form.password.value = '';
                form.role_id.value = data.user.roles[0]?.id || '';
                
                document.getElementById('modalTitle').textContent = 'Editar Usuario';
                document.getElementById('userModal').classList.remove('hidden');
            }
        });
}

function deleteUser(id) {
    if(confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        fetch(`/users/${id}`, {
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
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const method = currentUserId ? 'PUT' : 'POST';
    const url = currentUserId ? `/users/${currentUserId}` : '/users';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Remove password if empty
    if(!data.password) {
        delete data.password;
    }
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
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
