@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Editar tarjeta</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                    </p>
                </div>

                <span class="px-3 py-1 text-xs font-medium rounded-full
                    {{ $card->status === 'active'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300' }}">
                    {{ ucfirst($card->status) }}
                </span>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-900 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="px-3 py-1 rounded-full bg-blue-600 text-white">1. Información</span>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">2. Diseño</span>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">3. Detalles</span>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">4. Notificaciones</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Wizard en construcción
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Ya quedó creado el borrador escalable de la tarjeta.
                        El siguiente paso es construir el paso 1 del wizard según el tipo.
                    </p>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 text-left">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                            <p class="text-xs uppercase text-gray-400">Nombre</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $card->name }}</p>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                            <p class="text-xs uppercase text-gray-400">Slug</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $card->slug }}</p>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                            <p class="text-xs uppercase text-gray-400">Tipo</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $card->type->name }}</p>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                            <p class="text-xs uppercase text-gray-400">Código</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ strtoupper($card->code_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vista previa</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Preview inicial del borrador.
            </p>

            <div class="mt-6 flex justify-center">
                <div class="w-64 rounded-[2rem] border-8 border-gray-900 bg-gray-100 p-4 shadow-xl">
                    <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ $card->type->name }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $card->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400">Estado</p>
                                <p class="text-[10px] text-gray-500">{{ ucfirst($card->status) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                            Imagen principal
                        </div>

                        <div class="mt-4">
                            <p class="text-xs text-gray-500">Código</p>
                            <div class="mt-3 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                                {{ strtoupper($card->code_type) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                <p><strong>ID:</strong> {{ $card->id }}</p>
                <p><strong>Creada:</strong> {{ $card->created_at?->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
@endsection
