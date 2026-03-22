@extends('layouts.app')

@section('content')
    @php
        use App\Support\CardPreviewCode;

        $notification = $card->notification;
        $geoMessage = $notification?->settings_json['geo_message'] ?? 'Estás cerca de una sucursal.';

        $design = $card->design;

        $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
        $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
        $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;
        $stampActiveImageUrl = $design?->stamp_active_image_path ? asset('storage/' . $design->stamp_active_image_path) : null;
        $stampInactiveImageUrl = $design?->stamp_inactive_image_path ? asset('storage/' . $design->stamp_inactive_image_path) : null;

        $backgroundColor = $design?->background_color ?? '#F3F4F6';
        $activeColor = $design?->active_color ?? '#2563EB';
        $inactiveColor = $design?->inactive_color ?? '#D1D5DB';
        $textColor = $design?->text_color ?? '#111827';
        $codeType = $card->code_type ?? 'qr';

        $displayName = $card->settings_json['display_name'] ?? $card->name;

        $qrDataUri = CardPreviewCode::qrDataUri($card->id);
        $barcodeDataUri = CardPreviewCode::barcodeDataUri($card->id);
        $barcodeLabel = CardPreviewCode::barcodeLabel($card->id);

        $supportsStamps = $card->type->code === 'stamps';

        $stampsRequired = (int) ($card->config('stamps.required', 10) ?? 10);
        $stampsOnSignup = (int) ($card->config('stamps.on_signup', 0) ?? 0);
    @endphp

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_390px]"
        x-data="{
            autosave: null,
            previewPlatform: localStorage.getItem('uppy_preview_platform') || 'ios',

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

            design: {
                background_color: @js($backgroundColor),
                active_color: @js($activeColor),
                inactive_color: @js($inactiveColor),
                text_color: @js($textColor),
                code_type: @js($codeType),
            },

            logoHorizontalUrl: @js($logoHorizontalUrl),
            logoSquareUrl: @js($logoSquareUrl),
            mainImageUrl: @js($mainImageUrl),
            stampActiveImageUrl: @js($stampActiveImageUrl),
            stampInactiveImageUrl: @js($stampInactiveImageUrl),

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
            },

            setPreview(platform) {
                this.previewPlatform = platform;
                localStorage.setItem('uppy_preview_platform', platform);
            },

            activeNotifications() {
                const items = [];

                if (this.birthdayEnabled) {
                    items.push({
                        title: 'Cumpleaños',
                        body: this.form.birthday_message || 'Sin mensaje'
                    });
                }

                if (this.lastVisitEnabled) {
                    items.push({
                        title: 'Última visita',
                        body: this.form.last_visit_message || 'Sin mensaje'
                    });
                }

                if (this.expirationEnabled) {
                    items.push({
                        title: 'Vencimiento',
                        body: this.form.expiration_message || 'Sin mensaje'
                    });
                }

                if (this.purchaseEnabled) {
                    items.push({
                        title: 'Compra',
                        body: this.form.purchase_message || 'Sin mensaje'
                    });
                }

                if (this.rewardEnabled) {
                    items.push({
                        title: 'Recompensa',
                        body: this.form.reward_message || 'Sin mensaje'
                    });
                }

                if (this.geoEnabled) {
                    items.push({
                        title: 'Geolocalización',
                        body: this.form.geo_message || 'Sin mensaje'
                    });
                }

                return items.slice(0, 3);
            }
        }"
    >
        {{-- Panel principal --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
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

        {{-- Preview lateral tipo teléfono --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900">Vista previa</h3>
            <p class="mt-1 text-xs text-gray-500">Simulación visual por dispositivo.</p>

            <div class="mt-5 flex justify-center">
                <template x-if="previewPlatform === 'ios'">
                    <div class="relative w-[276px] rounded-[2.7rem] border-[7px] border-[#111111] bg-[#111111] p-[8px] shadow-[0_20px_45px_rgba(0,0,0,0.22)]">
                        <div class="absolute left-1/2 top-[10px] z-20 -translate-x-1/2 w-[74px] h-[19px] rounded-full bg-black shadow-inner"></div>

                        <div class="rounded-[2.15rem] overflow-hidden min-h-[560px] bg-[#f3f2f8] p-3 pt-9">
                            <div class="px-2">
                                <div
                                    class="rounded-[1.7rem] overflow-hidden border border-white/80 shadow-[0_18px_34px_rgba(0,0,0,0.16)]"
                                    :style="`background: linear-gradient(180deg, ${design.background_color} 0%, #ffffff 86%); color:${design.text_color}`"
                                >
                                    <div class="px-4 pt-4 pb-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <template x-if="logoHorizontalUrl">
                                                    <img :src="logoHorizontalUrl" class="h-6 object-contain shrink-0">
                                                </template>

                                                <template x-if="!logoHorizontalUrl && logoSquareUrl">
                                                    <img :src="logoSquareUrl" class="h-9 w-9 rounded-xl object-cover bg-white shadow-sm shrink-0">
                                                </template>

                                                <template x-if="!logoHorizontalUrl && !logoSquareUrl">
                                                    <div class="flex items-center justify-center h-9 w-9 rounded-xl bg-white text-xs font-semibold text-gray-700 shadow-sm shrink-0">
                                                        U
                                                    </div>
                                                </template>

                                                <div class="min-w-0">
                                                    <p class="text-[10px] opacity-55 truncate">{{ $card->type->name }}</p>
                                                    <p class="text-[15px] font-semibold leading-tight truncate">{{ $displayName }}</p>
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <p class="text-[10px] opacity-45">Estado</p>
                                                <p class="text-[10px]">{{ ucfirst($card->status) }}</p>
                                            </div>
                                        </div>

                                        @unless($supportsStamps)
                                            <div class="mt-4 h-24 rounded-[1.1rem] bg-white/70 overflow-hidden border border-white/70">
                                                <template x-if="mainImageUrl">
                                                    <img :src="mainImageUrl" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!mainImageUrl">
                                                    <div class="w-full h-full flex items-center justify-center text-sm text-gray-400">
                                                        Imagen principal
                                                    </div>
                                                </template>
                                            </div>
                                        @endunless

                                        @if($supportsStamps)
                                            <div class="mt-4 rounded-[1.15rem] bg-white/65 border border-white/70 px-3 py-3">
                                                <div class="flex items-center justify-between mb-3">
                                                    <p class="text-[11px] font-medium">Sellos</p>
                                                    <p class="text-[10px] opacity-60">{{ $stampsOnSignup }}/{{ $stampsRequired }}</p>
                                                </div>

                                                <div class="grid grid-cols-5 gap-2">
                                                    @for($i = 1; $i <= max(5, min(10, $stampsRequired)); $i++)
                                                        @if($i <= $stampsOnSignup)
                                                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm overflow-hidden"
                                                                 style="background: {{ $activeColor }}; color:white;">
                                                                @if($stampActiveImageUrl)
                                                                    <img src="{{ $stampActiveImageUrl }}" class="w-full h-full object-cover" alt="">
                                                                @else
                                                                    ★
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm overflow-hidden"
                                                                 style="background: {{ $inactiveColor }}; color: {{ $textColor }}; opacity:.88;">
                                                                @if($stampInactiveImageUrl)
                                                                    <img src="{{ $stampInactiveImageUrl }}" class="w-full h-full object-cover" alt="">
                                                                @else
                                                                    ●
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-4 rounded-[1.1rem] bg-white/80 border border-white/70 p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-[10px] uppercase tracking-wide opacity-50">Notificaciones activas</p>
                                                <p class="text-[10px] opacity-50" x-text="activeNotifications().length"></p>
                                            </div>

                                            <div class="space-y-2">
                                                <template x-for="(item, index) in activeNotifications()" :key="'ios-notif-'+index">
                                                    <div class="rounded-xl bg-white/70 border border-white/60 px-3 py-2">
                                                        <p class="text-[11px] font-semibold" x-text="item.title"></p>
                                                        <p class="mt-1 text-[10px] opacity-70 line-clamp-2" x-text="item.body"></p>
                                                    </div>
                                                </template>

                                                <template x-if="activeNotifications().length === 0">
                                                    <div class="rounded-xl bg-white/70 border border-white/60 px-3 py-2 text-[11px] opacity-50">
                                                        Sin notificaciones activas
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <p class="text-xs opacity-60 mb-2">Código</p>
                                            <div class="rounded-[1.1rem] bg-white/80 border border-white/70 p-4 min-h-[122px] flex flex-col items-center justify-center">
                                                <template x-if="design.code_type === 'qr'">
                                                    <img :src="@js($qrDataUri)" class="w-24 h-24 object-contain">
                                                </template>

                                                <template x-if="design.code_type === 'barcode'">
                                                    <div class="w-full text-center">
                                                        <img :src="@js($barcodeDataUri)" class="mx-auto h-14 object-contain">
                                                        <p class="mt-2 text-[10px] tracking-widest text-gray-500">{{ $barcodeLabel }}</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="previewPlatform === 'android'">
                    <div class="relative w-[280px] rounded-[2.45rem] border-[7px] border-[#151515] bg-[#151515] p-[8px] shadow-[0_20px_45px_rgba(0,0,0,0.22)]">
                        <div class="absolute left-1/2 top-[11px] z-20 -translate-x-1/2 h-[10px] w-[10px] rounded-full bg-black border border-neutral-700"></div>

                        <div class="rounded-[1.95rem] overflow-hidden min-h-[560px] bg-[#f6f7f8] p-3 pt-7">
                            <div
                                class="overflow-hidden rounded-[1.35rem] border border-gray-200 bg-white shadow-[0_10px_24px_rgba(0,0,0,0.10)]"
                                :style="`color:${design.text_color}`"
                            >
                                <div
                                    class="px-4 pt-4 pb-3"
                                    :style="`background: linear-gradient(135deg, ${design.background_color} 0%, ${design.active_color} 100%); color: white;`"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <template x-if="logoSquareUrl">
                                                <img :src="logoSquareUrl" class="h-9 w-9 rounded-full object-cover border border-white/40 shadow-sm shrink-0 bg-white">
                                            </template>

                                            <template x-if="!logoSquareUrl && logoHorizontalUrl">
                                                <img :src="logoHorizontalUrl" class="h-6 object-contain shrink-0 brightness-[1.15]">
                                            </template>

                                            <template x-if="!logoSquareUrl && !logoHorizontalUrl">
                                                <div class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center text-xs font-semibold shrink-0">
                                                    U
                                                </div>
                                            </template>

                                            <div class="min-w-0">
                                                <p class="text-[10px] text-white/75 truncate">{{ $card->type->name }}</p>
                                                <p class="text-[15px] font-semibold leading-tight truncate text-white">{{ $displayName }}</p>
                                            </div>
                                        </div>

                                        <div class="text-right text-[10px] text-white/75 shrink-0">
                                            <p>Estado</p>
                                            <p>{{ ucfirst($card->status) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-4 bg-white">
                                    @unless($supportsStamps)
                                        <div class="h-20 rounded-[0.95rem] overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400 border border-gray-100">
                                            <template x-if="mainImageUrl">
                                                <img :src="mainImageUrl" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!mainImageUrl">
                                                <span>Imagen principal</span>
                                            </template>
                                        </div>
                                    @endunless

                                    @if($supportsStamps)
                                        <div class="mt-1">
                                            <p class="mb-2 text-[11px] font-medium text-gray-500">Progreso</p>
                                            <div class="grid grid-cols-5 gap-2">
                                                @for($i = 1; $i <= max(5, min(10, $stampsRequired)); $i++)
                                                    @if($i <= $stampsOnSignup)
                                                        <div class="h-8 w-8 rounded-lg flex items-center justify-center text-sm font-bold border overflow-hidden"
                                                             style="background: {{ $activeColor }}; color:white; border-color: {{ $activeColor }};">
                                                            @if($stampActiveImageUrl)
                                                                <img src="{{ $stampActiveImageUrl }}" class="w-full h-full object-cover" alt="">
                                                            @else
                                                                ★
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="h-8 w-8 rounded-lg flex items-center justify-center text-sm font-bold border overflow-hidden"
                                                             style="background:white; color: {{ $inactiveColor }}; border-color: {{ $inactiveColor }};">
                                                            @if($stampInactiveImageUrl)
                                                                <img src="{{ $stampInactiveImageUrl }}" class="w-full h-full object-cover" alt="">
                                                            @else
                                                                ●
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-4 rounded-xl bg-gray-50 p-3 border border-gray-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-[10px] uppercase tracking-wide text-gray-400">Notificaciones activas</p>
                                            <p class="text-[10px] text-gray-400" x-text="activeNotifications().length"></p>
                                        </div>

                                        <div class="space-y-2">
                                            <template x-for="(item, index) in activeNotifications()" :key="'android-notif-'+index">
                                                <div class="rounded-xl bg-white border border-gray-100 px-3 py-2">
                                                    <p class="text-[11px] font-semibold text-gray-800" x-text="item.title"></p>
                                                    <p class="mt-1 text-[10px] text-gray-500 line-clamp-2" x-text="item.body"></p>
                                                </div>
                                            </template>

                                            <template x-if="activeNotifications().length === 0">
                                                <div class="text-[11px] text-gray-400">Sin notificaciones activas</div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="mt-5 rounded-[1rem] bg-[#f8fafc] p-4 border border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs font-medium text-gray-500">Código</p>
                                            <div class="h-1.5 w-10 rounded-full" :style="`background:${design.active_color}`"></div>
                                        </div>

                                        <template x-if="design.code_type === 'qr'">
                                            <div class="flex justify-center">
                                                <img :src="@js($qrDataUri)" class="w-24 h-24 object-contain">
                                            </div>
                                        </template>

                                        <template x-if="design.code_type === 'barcode'">
                                            <div class="text-center">
                                                <img :src="@js($barcodeDataUri)" class="mx-auto h-14 object-contain">
                                                <p class="mt-2 text-[10px] tracking-widest text-gray-500">{{ $barcodeLabel }}</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-4 space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        @click="setPreview('ios')"
                        :class="previewPlatform === 'ios'
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    >
                        <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-900 text-white text-xs"></span>
                        iOS
                    </button>

                    <button
                        type="button"
                        @click="setPreview('android')"
                        :class="previewPlatform === 'android'
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    >
                        <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-600 text-white text-xs font-bold">A</span>
                        Android
                    </button>
                </div>
            </div>

            <div class="mt-5 text-xs text-gray-500 space-y-1">
                <p><span class="font-medium text-gray-700">Activas:</span> <span x-text="activeNotifications().length"></span></p>
                <p><span class="font-medium text-gray-700">Preview:</span> <span x-text="previewPlatform"></span></p>
                <p><span class="font-medium text-gray-700">Código:</span> {{ $codeType }}</p>
            </div>
        </div>
    </div>
@endsection
