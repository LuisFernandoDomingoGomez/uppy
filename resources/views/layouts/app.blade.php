<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Uppy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <x-layout.sidebar />

        <div class="lg:ml-64">
            <x-layout.navbar />

            <main class="p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
