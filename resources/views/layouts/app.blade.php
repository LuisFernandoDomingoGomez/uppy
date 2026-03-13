<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Uppy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <x-layout.sidebar />

        <div class="lg:ml-64">
            <x-layout.navbar />

            <main class="p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        window.uppyAutosave = function ({ url, section, indicatorId = 'autosave-indicator' }) {
            let timeout = null;
            let saving = false;
            let pendingPayload = null;

            const indicator = () => document.getElementById(indicatorId);

            const setStatus = (text, cls = 'text-gray-400') => {
                const el = indicator();
                if (!el) return;
                el.textContent = text;
                el.className = `text-xs ${cls}`;
            };

            const safeJson = async (response) => {
                const contentType = response.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    return await response.json();
                }

                const text = await response.text();
                return {
                    ok: false,
                    message: text || 'Respuesta no válida del servidor.',
                };
            };

            const doSend = async (payload) => {
                saving = true;
                setStatus('Guardando...', 'text-amber-500');

                try {
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            section,
                            ...payload,
                        }),
                    });

                    const data = await safeJson(response);

                    if (!response.ok || data.ok === false) {
                        throw new Error(data.message || 'No se pudo guardar.');
                    }

                    setStatus('Guardado', 'text-emerald-600');
                } catch (error) {
                    console.error('Autosave error:', error);
                    setStatus('Error al guardar', 'text-red-500');
                } finally {
                    saving = false;

                    if (pendingPayload) {
                        const nextPayload = pendingPayload;
                        pendingPayload = null;
                        await doSend(nextPayload);
                    }
                }
            };

            return {
                queue(payload) {
                    clearTimeout(timeout);
                    setStatus('Cambios sin guardar', 'text-slate-400');

                    timeout = setTimeout(() => {
                        if (saving) {
                            pendingPayload = payload;
                            return;
                        }

                        doSend(payload);
                    }, 700);
                },

                async flush(payload) {
                    clearTimeout(timeout);

                    if (saving) {
                        pendingPayload = payload;
                        return;
                    }

                    await doSend(payload);
                }
            };
        };
    </script>
</body>
</html>
