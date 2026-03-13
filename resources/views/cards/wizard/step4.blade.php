@extends('layouts.app')

@section('content')
    @php
        $notification = $card->notification;
        $geoMessage = $notification?->settings_json['geo_message'] ?? 'Estás cerca de una sucursal.';
    @endphp

    <div
        class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden"
        x-data="{
            autosave: null,

            birthdayEnabled: {{ old('birthday_enabled', $notification?->birthday_enabled ? 'true' : 'false') }},
            lastVisitEnabled: {{ old('last_visit_enabled', $notification?->last_visit_enabled ? 'true' : 'false') }},
            expirationEnabled: {{ old('expiration_enabled', $notification?->expiration_enabled ? 'true' : 'false') }},
            purchaseEnabled: {{ old('purchase_enabled', $notification?->purchase_enabled ? 'true' : 'false') }},
            rewardEnabled: {{ old('reward_enabled', $notification?->reward_enabled ? 'true' : 'false') }},
            geoEnabled: {{ old('geo_enabled', $notification?->geo_enabled ? 'true' : 'false') }},

            form: {
                birthday_enabled: {{ old('birthday_enabled', $notification?->birthday_enabled ? 'true' : 'false') }},
                birthday_days_before: @js(old('birthday_days_before', $notification?->birthday_days_before ?? 7)),
                birthday_message: @js(old('birthday_message', $notification?->birthday_message ?? 'Feliz cumpleaños! 🎂🎉 Esperamos que tengas un día increíble.')),

                last_visit_enabled: {{ old('last_visit_enabled', $notification?->last_visit_enabled ? 'true' : 'false') }},
                last_visit_days_after: @js(old('last_visit_days_after', $notification?->last_visit_days ?? 30)),
                last_visit_message: @js(old('last_visit_message', $notification?->last_visit_message ?? 'Ha pasado tiempo desde tu última visita 🙂 Te extrañamos. Ven pronto y sigue disfrutando de tus beneficios.')),

                expiration_enabled: {{ old('expiration_enabled', $notification?->expiration_enabled ? 'true' : 'false') }},
                expiration_message: @js(old('expiration_message', $notification?->expiration_message ?? '¡Ups! Tu tarjeta ha vencido y queremos que sigas disfrutando de tus beneficios.')),

                purchase_enabled: {{ old('purchase_enabled', $notification?->purchase_enabled ? 'true' : 'false') }},
                purchase_message: @js(old('purchase_message', $notification?->purchase_message ?? '¡Gracias por tu compra! 💗 Nos encanta verte disfrutar de nuestros productos. ¡Nos vemos pronto!')),

                reward_enabled: {{ old('reward_enabled', $notification?->reward_enabled ? 'true' : 'false') }},
                reward_message: @js(old('reward_message', $notification?->reward_message ?? '¡Ya tienes una recompensa disponible! 🎁')),

                geo_enabled: {{ old('geo_enabled', $notification?->geo_enabled ? 'true' : 'false') }},
                geo_radius_meters: @js(old('geo_radius_meters', $notification?->geo_radius_meters ?? 100)),
                geo_message: @js(old('geo_message', $geoMessage)),
            },

            init() {
                this.autosave = window.uppyAutosave({
                    url: '{{ route('cards.autosave', $card) }}',
                    section: 'step4'
                });
            },

            changed() {
                this.form.birthday_enabled = this.birthdayEnabled;
                this.form.last_visit_enabled = this.lastVisitEnabled;
                this.form.expiration_enabled = this.expirationEnabled;
                this.form.purchase_enabled = this.purchaseEnabled;
                this.form.reward_enabled = this.rewardEnabled;
                this.form.geo_enabled = this.geoEnabled;

                this.autosave.queue(this.form);
            }
        }"
    >
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Notificaciones</h2>
                <p class="text-sm text-gray-500">
                    Configura cuándo y cómo se enviarán mensajes automáticos a tus clientes.
                </p>
            </div>

            <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                Draft
            </span>
        </div>

        {{-- Indicador autosave --}}
        <div class="px-6 pt-3">
            <div id="autosave-indicator" class="text-xs text-gray-400">
                Guardado
            </div>
        </div>

        {{-- Tabs --}}
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
                    Detalles
                </a>

                <div class="relative pb-2 text-blue-600 border-b-2 border-blue-600">
                    Notificaciones
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            @if(session('success'))
                <div class="mb-5 px-4 py-3 text-sm text-green-800 bg-green-50 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 px-4 py-3 text-sm text-red-800 bg-red-50 rounded-lg">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-8">
                {{-- Cumpleaños --}}
                <section class="border-b border-gray-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            type="checkbox"
                            x-model="birthdayEnabled"
                            @change="changed()"
                            class="rounded border-gray-300"
                        >
                        <h3 class="text-base font-semibold text-gray-900">Cumpleaños</h3>
                    </div>

                    <div x-show="birthdayEnabled" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <div class="lg:col-span-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Días antes</label>
                            <input
                                type="number"
                                min="0"
                                x-model="form.birthday_days_before"
                                @input="changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>

                        <div class="lg:col-span-9">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Mensaje</label>
                            <textarea
                                x-model="form.birthday_message"
                                @input="changed()"
                                rows="3"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            ></textarea>
                        </div>
                    </div>
                </section>

                {{-- Última visita --}}
                <section class="border-b border-gray-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            type="checkbox"
                            x-model="lastVisitEnabled"
                            @change="changed()"
                            class="rounded border-gray-300"
                        >
                        <h3 class="text-base font-semibold text-gray-900">Recordatorio de última visita</h3>
                    </div>

                    <div x-show="lastVisitEnabled" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <div class="lg:col-span-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Días después</label>
                            <input
                                type="number"
                                min="1"
                                x-model="form.last_visit_days_after"
                                @input="changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>

                        <div class="lg:col-span-9">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Mensaje</label>
                            <textarea
                                x-model="form.last_visit_message"
                                @input="changed()"
                                rows="3"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            ></textarea>
                        </div>
                    </div>
                </section>

                {{-- Vencimiento --}}
                <section class="border-b border-gray-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            type="checkbox"
                            x-model="expirationEnabled"
                            @change="changed()"
                            class="rounded border-gray-300"
                        >
                        <h3 class="text-base font-semibold text-gray-900">Vencimiento de tarjeta</h3>
                    </div>

                    <div x-show="expirationEnabled" x-cloak>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea
                            x-model="form.expiration_message"
                            @input="changed()"
                            rows="3"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                        ></textarea>
                    </div>
                </section>

                {{-- Compra --}}
                <section class="border-b border-gray-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            type="checkbox"
                            x-model="purchaseEnabled"
                            @change="changed()"
                            class="rounded border-gray-300"
                        >
                        <h3 class="text-base font-semibold text-gray-900">Al comprar</h3>
                    </div>

                    <div x-show="purchaseEnabled" x-cloak>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea
                            x-model="form.purchase_message"
                            @input="changed()"
                            rows="3"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                        ></textarea>
                    </div>
                </section>

                {{-- Recompensa --}}
                <section class="border-b border-gray-200 pb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            type="checkbox"
                            x-model="rewardEnabled"
                            @change="changed()"
                            class="rounded border-gray-300"
                        >
                        <h3 class="text-base font-semibold text-gray-900">Al alcanzar recompensa</h3>
                    </div>

                    <div x-show="rewardEnabled" x-cloak>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea
                            x-model="form.reward_message"
                            @input="changed()"
                            rows="3"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                        ></textarea>
                    </div>
                </section>

                {{-- Geolocalización --}}
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            type="checkbox"
                            x-model="geoEnabled"
                            @change="changed()"
                            class="rounded border-gray-300"
                        >
                        <h3 class="text-base font-semibold text-gray-900">Notificaciones geolocalizadas</h3>
                    </div>

                    <div x-show="geoEnabled" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <div class="lg:col-span-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Radio en metros</label>
                            <input
                                type="number"
                                min="1"
                                x-model="form.geo_radius_meters"
                                @input="changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>

                        <div class="lg:col-span-9">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Mensaje</label>
                            <textarea
                                x-model="form.geo_message"
                                @input="changed()"
                                rows="3"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            ></textarea>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Navegación --}}
            <div class="flex items-center justify-between pt-8 mt-8 border-t border-gray-200">
                <a href="{{ route('cards.wizard.step3', $card) }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                    Anterior
                </a>

                <a href="{{ route('cards.show', $card) }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-emerald-600 rounded-md hover:bg-emerald-700 transition">
                    Ver tarjeta
                </a>
            </div>
        </div>
    </div>
@endsection
