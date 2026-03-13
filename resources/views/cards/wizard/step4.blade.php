@extends('layouts.app')

@section('content')
    @php
        $notification = $card->notification;
        $design = $card->design;
        $previewPlatform = $design?->preview_platform ?? 'ios';
        $geoMessage = $notification?->settings_json['geo_message'] ?? 'Estás cerca de una sucursal.';
    @endphp

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_280px]"
        x-data="{
            birthdayEnabled: {{ old('birthday_enabled', $notification?->birthday_enabled ? 'true' : 'false') }},
            lastVisitEnabled: {{ old('last_visit_enabled', $notification?->last_visit_enabled ? 'true' : 'false') }},
            expirationEnabled: {{ old('expiration_enabled', $notification?->expiration_enabled ? 'true' : 'false') }},
            purchaseEnabled: {{ old('purchase_enabled', $notification?->purchase_enabled ? 'true' : 'false') }},
            rewardEnabled: {{ old('reward_enabled', $notification?->reward_enabled ? 'true' : 'false') }},
            geoEnabled: {{ old('geo_enabled', $notification?->geo_enabled ? 'true' : 'false') }},
        }"
    >
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <form action="{{ route('cards.wizard.step4.save', $card) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar tarjeta</h2>
                        <p class="text-sm text-gray-500">
                            Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition"
                    >
                        Guardar
                    </button>
                </div>

                <div class="px-6 pt-4 pb-3 border-b border-gray-200">
                    <div class="grid grid-cols-4 gap-2 text-xs sm:text-sm">
                        <a href="{{ route('cards.wizard.step1', $card) }}"
                           class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                            Información
                        </a>

                        <a href="{{ route('cards.wizard.step2', $card) }}"
                           class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                            Diseño
                        </a>

                        <a href="{{ route('cards.wizard.step3', $card) }}"
                           class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                            Detalles de la tarjeta
                        </a>

                        <div class="relative pb-2 text-blue-600 border-b-2 border-blue-600">
                            Notificaciones
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4">
                    @if(session('success'))
                        <div class="mb-4 px-4 py-3 text-sm text-green-800 bg-green-50 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 px-4 py-3 text-sm text-red-800 bg-red-50 rounded-lg">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        {{-- Cumpleaños --}}
                        <div class="border-b border-gray-200 pb-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-800 mb-2">
                                <input type="checkbox" name="birthday_enabled" value="1" x-model="birthdayEnabled" class="rounded border-gray-300">
                                Cumpleaños
                            </label>

                            <div x-show="birthdayEnabled" x-cloak class="space-y-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Días antes del cumpleaños</label>
                                    <input
                                        type="number"
                                        name="birthday_days_before"
                                        value="{{ old('birthday_days_before', $notification?->birthday_days_before ?? 7) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Mensaje</label>
                                    <textarea
                                        name="birthday_message"
                                        rows="2"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                    >{{ old('birthday_message', $notification?->birthday_message ?? 'Feliz cumpleaños! 🎂🎉 Esperamos que tengas un día increíble.') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Última visita --}}
                        <div class="border-b border-gray-200 pb-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-800 mb-2">
                                <input type="checkbox" name="last_visit_enabled" value="1" x-model="lastVisitEnabled" class="rounded border-gray-300">
                                Recordatorio de última visita
                            </label>

                            <div x-show="lastVisitEnabled" x-cloak class="space-y-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Días desde la última visita</label>
                                    <input
                                        type="number"
                                        name="last_visit_days_after"
                                        value="{{ old('last_visit_days_after', $notification?->last_visit_days ?? 30) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Mensaje</label>
                                    <textarea
                                        name="last_visit_message"
                                        rows="2"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                    >{{ old('last_visit_message', $notification?->last_visit_message ?? 'Ha pasado tiempo desde tu última visita 🙂 Te extrañamos. Ven pronto y sigue disfrutando de tus beneficios.') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Vencimiento --}}
                        <div class="border-b border-gray-200 pb-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-800 mb-2">
                                <input type="checkbox" name="expiration_enabled" value="1" x-model="expirationEnabled" class="rounded border-gray-300">
                                Vencimiento de tarjeta
                            </label>

                            <div x-show="expirationEnabled" x-cloak>
                                <label class="block text-xs text-gray-500 mb-1">Mensaje</label>
                                <textarea
                                    name="expiration_message"
                                    rows="2"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                >{{ old('expiration_message', $notification?->expiration_message ?? '¡Ups! Tu tarjeta ha vencido y queremos que sigas disfrutando de tus beneficios.') }}</textarea>
                            </div>
                        </div>

                        {{-- Compra --}}
                        <div class="border-b border-gray-200 pb-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-800 mb-2">
                                <input type="checkbox" name="purchase_enabled" value="1" x-model="purchaseEnabled" class="rounded border-gray-300">
                                Al comprar
                            </label>

                            <div x-show="purchaseEnabled" x-cloak>
                                <label class="block text-xs text-gray-500 mb-1">Mensaje</label>
                                <textarea
                                    name="purchase_message"
                                    rows="2"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                >{{ old('purchase_message', $notification?->purchase_message ?? '¡Gracias por tu compra! 💗 Nos encanta verte disfrutar de nuestros productos. ¡Nos vemos pronto!') }}</textarea>
                            </div>
                        </div>

                        {{-- Recompensa --}}
                        <div class="border-b border-gray-200 pb-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-800 mb-2">
                                <input type="checkbox" name="reward_enabled" value="1" x-model="rewardEnabled" class="rounded border-gray-300">
                                Al alcanzar recompensa
                            </label>

                            <div x-show="rewardEnabled" x-cloak>
                                <label class="block text-xs text-gray-500 mb-1">Mensaje</label>
                                <textarea
                                    name="reward_message"
                                    rows="2"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                >{{ old('reward_message', $notification?->reward_message ?? '¡Ya tienes una recompensa disponible! 🎁') }}</textarea>
                            </div>
                        </div>

                        {{-- Geolocalización --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-2">Notificaciones geolocalizadas</p>

                            <label class="flex items-center gap-2 text-sm font-medium text-gray-800 mb-2">
                                <input type="checkbox" name="geo_enabled" value="1" x-model="geoEnabled" class="rounded border-gray-300">
                                Activar notificaciones geolocalizadas
                            </label>

                            <div x-show="geoEnabled" x-cloak class="space-y-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Radio en metros</label>
                                    <input
                                        type="number"
                                        name="geo_radius_meters"
                                        value="{{ old('geo_radius_meters', $notification?->geo_radius_meters ?? 100) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Mensaje</label>
                                    <textarea
                                        name="geo_message"
                                        rows="2"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md"
                                    >{{ old('geo_message', $geoMessage) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
                    <a href="{{ route('cards.wizard.step3', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Anterior
                    </a>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition"
                        >
                            Guardar paso 4
                        </button>

                        <a href="{{ route('cards.show', $card) }}"
                           class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-emerald-600 rounded-md hover:bg-emerald-700 transition">
                            Finalizar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900">Resumen</h3>
            <p class="mt-1 text-xs text-gray-500">Configura qué mensajes estarán activos.</p>

            <div class="mt-5 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Cumpleaños</span>
                    <span x-text="birthdayEnabled ? 'Activo' : 'Inactivo'" class="text-gray-900"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Última visita</span>
                    <span x-text="lastVisitEnabled ? 'Activo' : 'Inactivo'" class="text-gray-900"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Vencimiento</span>
                    <span x-text="expirationEnabled ? 'Activo' : 'Inactivo'" class="text-gray-900"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Compra</span>
                    <span x-text="purchaseEnabled ? 'Activo' : 'Inactivo'" class="text-gray-900"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Recompensa</span>
                    <span x-text="rewardEnabled ? 'Activo' : 'Inactivo'" class="text-gray-900"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Geolocalización</span>
                    <span x-text="geoEnabled ? 'Activo' : 'Inactivo'" class="text-gray-900"></span>
                </div>

                <div class="pt-3 text-[11px] text-gray-400">
                    Plataforma actual: {{ $previewPlatform }}
                </div>
            </div>
        </div>
    </div>
@endsection
