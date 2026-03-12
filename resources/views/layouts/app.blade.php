<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Uppy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        <x-layout.sidebar />

        <div class="p-4 sm:ml-64">
            <x-layout.navbar />

            <main class="mt-4">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
