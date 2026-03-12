@extends('layouts.app')

@section('content')
    <div class="max-w-4xl">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Editar rol</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Actualiza el rol y sus permisos</p>
            </div>

            <form action="{{ route('roles.update', $role) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del rol</label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}"
                           class="w-full p-3 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">Permisos</label>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach($permissions as $permission)
                            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg dark:border-gray-700">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       @checked(in_array($permission->name, old('permissions', $rolePermissions)))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('roles.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-white">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Actualizar rol
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
