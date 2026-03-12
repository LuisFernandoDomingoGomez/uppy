@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Usuarios totales</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</h2>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Roles registrados</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalRoles }}</h2>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Administradores</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $adminUsers }}</h2>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Tu rol</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                {{ ucfirst(auth()->user()->roles->first()?->name ?? 'Sin rol') }}
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Resumen general</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Estado actual del sistema
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-300">Usuarios con rol admin</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $adminUsers }}</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-300">Usuarios normales</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers - $adminUsers }}</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-300">Módulo activo</p>
                    <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">Usuarios</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-300">Estado</p>
                    <p class="mt-2 text-xl font-bold text-green-600">Operativo</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Usuarios recientes</h3>

            <div class="mt-4 space-y-4">
                @forelse($recentUsers as $user)
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $user->created_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @empty
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-300">No hay usuarios recientes.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
