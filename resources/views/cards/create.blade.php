@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Nueva tarjeta</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Selecciona el tipo de tarjeta que deseas crear
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-4">
                @foreach($cardTypes as $type)
                    <form action="{{ route('cards.store-draft') }}" method="POST">
                        @csrf
                        <input type="hidden" name="card_type_id" value="{{ $type->id }}">

                        <button type="submit"
                            class="w-full text-left p-5 border border-gray-200 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition dark:border-gray-700 dark:hover:bg-gray-700">
                            <div class="flex items-start gap-4">
                                <div class="flex items-center justify-center w-14 h-14 text-blue-600 bg-blue-100 rounded-2xl dark:bg-blue-900 dark:text-blue-300">
                                    <span class="text-lg font-bold">{{ strtoupper(substr($type->name, 0, 1)) }}</span>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $type->name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $type->description }}
                                    </p>
                                </div>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vista previa</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tu tarjeta se verá aquí mientras avanzamos en el editor.
            </p>

            <div class="mt-6 flex justify-center">
                <div class="w-64 rounded-[2rem] border-8 border-gray-900 bg-gray-100 p-4 shadow-xl">
                    <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Tarjeta</p>
                                <p class="text-sm font-semibold text-gray-900">Nueva tarjeta</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400">Vencimiento</p>
                                <p class="text-[10px] text-gray-500">Sin definir</p>
                            </div>
                        </div>

                        <div class="mt-4 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                            Imagen principal
                        </div>

                        <div class="mt-4">
                            <p class="text-xs text-gray-500">Vista previa del código</p>
                            <div class="mt-3 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                                QR / Barras
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
