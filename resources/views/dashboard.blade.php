@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Usuarios</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">128</h2>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Tarjetas</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">54</h2>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Ventas</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">$12,450</h2>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Actividad</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">87%</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Resumen general</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Aquí irá el gráfico principal.
            </p>
            <div class="mt-6 h-80 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actividad reciente</h3>
            <div class="mt-4 space-y-4">
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700">Nuevo usuario registrado</div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700">Tarjeta actualizada</div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700">Cambio de rol realizado</div>
            </div>
        </div>
    </div>
@endsection
