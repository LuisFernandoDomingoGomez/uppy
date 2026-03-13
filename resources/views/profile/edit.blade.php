@extends('layouts.app')

@section('content')
    <div class="max-w-5xl grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center w-24 h-24 text-3xl font-bold text-white bg-blue-600 rounded-full">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->email }}
                    </p>

                    <span class="mt-3 inline-flex px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                        {{ ucfirst($user->roles->first()?->name ?? 'Sin rol') }}
                    </span>
                </div>

                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Miembro desde</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $user->created_at?->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Rol actual</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                            {{ ucfirst($user->roles->first()?->name ?? 'Sin rol') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Perfil de usuario</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Actualiza tu información personal y contraseña
                    </p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if(session('success'))
                        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nombre
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Cambiar contraseña
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Llena estos campos solo si deseas cambiarla
                        </p>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Contraseña actual
                        </label>
                        <input
                            type="password"
                            name="current_password"
                            class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nueva contraseña
                            </label>
                            <input
                                type="password"
                                name="password"
                                class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Confirmar nueva contraseña
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
