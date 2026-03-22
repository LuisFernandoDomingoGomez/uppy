@extends('layouts.app')

@section('content')
    @php
        use App\Support\CardPreviewCode;

        $typeCode = $card->type->code;
        $design = $card->design;

        $displayName = $card->settings_json['display_name'] ?? $card->name;

        $expirationMode = old('expiration_mode', $card->config('general.expiration_mode', 'unlimited'));
        $expiresAt = old('expires_at', $card->config('general.expires_at'));
        $expiresAfterValue = old('expires_after_value', $card->config('general.expires_after_value', 1));
        $expiresAfterUnit = old('expires_after_unit', $card->config('general.expires_after_unit', 'days'));
        $isUnlimited = old('is_unlimited', (bool) $card->is_unlimited);

        $stampsEarningMode = old('stamps_earning_mode', $card->config('stamps.earning_mode', 'visit'));
        $purchaseAmountRequired = old('purchase_amount_required', $card->config('stamps.purchase_amount_required'));
        $eventDescription = old('event_description', $card->config('stamps.event_description'));
        $stampsRequired = old('stamps_required', $card->config('stamps.required', 10));
        $stampsOnSignup = old('stamps_on_signup', $card->config('stamps.on_signup', 0));
        $rewardText = old('reward_text', $card->config('stamps.reward_text'));
        $dailyLimitEnabled = old('daily_limit_enabled', (bool) $card->config('stamps.daily_limit_enabled', false));

        $couponValidFor = old('coupon_valid_for', $card->config('coupon.valid_for'));
        $couponReplaceOnRedeem = old('coupon_replace_on_redeem', (bool) $card->config('coupon.replace_on_redeem', false));

        $giftcardInitialBalance = old('giftcard_initial_balance', $card->config('giftcard.initial_balance', 0));

        $pointsPerPurchase = old('points_per_purchase', $card->config('points.per_purchase', 1));
        $pointsRewardText = old('reward_text', $card->config('points.reward_text'));

        $tiers = old('tiers', $card->tiers->map(fn ($tier) => [
            'name' => $tier->name,
            'threshold_amount' => $tier->threshold_amount,
            'percentage' => $tier->percentage,
        ])->values()->toArray());

        if (empty($tiers) && in_array($typeCode, ['cashback', 'discount_levels'])) {
            $tiers = [
                ['name' => 'Bronce', 'threshold_amount' => 1000, 'percentage' => 3],
                ['name' => 'Plata', 'threshold_amount' => 3000, 'percentage' => 5],
                ['name' => 'Oro', 'threshold_amount' => 5000, 'percentage' => 10],
            ];
        }

        $backgroundColor = $design?->background_color ?? '#F3F4F6';
        $activeColor = $design?->active_color ?? '#2563EB';
        $inactiveColor = $design?->inactive_color ?? '#D1D5DB';
        $textColor = $design?->text_color ?? '#111827';
        $codeType = $card->code_type ?? 'qr';

        $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
        $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
        $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;
        $stampActiveImageUrl = $design?->stamp_active_image_path ? asset('storage/' . $design->stamp_active_image_path) : null;
        $stampInactiveImageUrl = $design?->stamp_inactive_image_path ? asset('storage/' . $design->stamp_inactive_image_path) : null;

        $qrDataUri = CardPreviewCode::qrDataUri($card->id);
        $barcodeDataUri = CardPreviewCode::barcodeDataUri($card->id);
        $barcodeLabel = CardPreviewCode::barcodeLabel($card->id);

        $supportsStamps = $typeCode === 'stamps';
    @endphp

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_390px]"
        x-data="{
            typeCode: @js($typeCode),
            expirationMode: @js($expirationMode),
            stampsEarningMode: @js($stampsEarningMode),
            previewPlatform: localStorage.getItem('uppy_preview_platform') || 'ios',

            autosave: null,

            form: {
                name: @js(old('name', $card->name)),
                expiration_mode: @js($expirationMode),
                expires_at: @js($expiresAt),
                expires_after_value: @js($expiresAfterValue),
                expires_after_unit: @js($expiresAfterUnit),
                is_unlimited: @js((bool) $isUnlimited),

                stamps_earning_mode: @js($stampsEarningMode),
                purchase_amount_required: @js($purchaseAmountRequired),
                event_description: @js($eventDescription),
                stamps_required: @js($stampsRequired),
                stamps_on_signup: @js($stampsOnSignup),
                reward_text: @js($rewardText),
                daily_limit_enabled: @js((bool) $dailyLimitEnabled),

                coupon_valid_for: @js($couponValidFor),
                coupon_replace_on_redeem: @js((bool) $couponReplaceOnRedeem),

                giftcard_initial_balance: @js($giftcardInitialBalance),

                points_per_purchase: @js($pointsPerPurchase),
                points_reward_text: @js($pointsRewardText),

                tiers: @js($tiers),
            },

            logoHorizontalUrl: @js($logoHorizontalUrl),
            logoSquareUrl: @js($logoSquareUrl),
            mainImageUrl: @js($mainImageUrl),
            stampActiveImageUrl: @js($stampActiveImageUrl),
            stampInactiveImageUrl: @js($stampInactiveImageUrl),

            design: {
                background_color: @js($backgroundColor),
                active_color: @js($activeColor),
                inactive_color: @js($inactiveColor),
                text_color: @js($textColor),
                code_type: @js($codeType),
            },

            init() {
                this.autosave = window.uppyAutosave({
                    url: '{{ route('cards.autosave', $card) }}',
                    section: 'step1'
                });
            },

            changed() {
                const payload = {
                    name: this.form.name,
                    expiration_mode: this.expirationMode,
                    expires_at: this.form.expires_at,
                    expires_after_value: this.form.expires_after_value,
                    expires_after_unit: this.form.expires_after_unit,
                    is_unlimited: this.form.is_unlimited,

                    stamps_earning_mode: this.stampsEarningMode,
                    purchase_amount_required: this.form.purchase_amount_required,
                    event_description: this.form.event_description,
                    stamps_required: this.form.stamps_required,
                    stamps_on_signup: this.form.stamps_on_signup,
                    reward_text: this.typeCode === 'points' ? this.form.points_reward_text : this.form.reward_text,
                    daily_limit_enabled: this.form.daily_limit_enabled,

                    coupon_valid_for: this.form.coupon_valid_for,
                    coupon_replace_on_redeem: this.form.coupon_replace_on_redeem,

                    giftcard_initial_balance: this.form.giftcard_initial_balance,

                    points_per_purchase: this.form.points_per_purchase,
                };

                this.autosave.queue(payload);
            },

            addTier() {
                this.form.tiers.push({
                    name: '',
                    threshold_amount: '',
                    percentage: ''
                });
            },

            removeTier(index) {
                this.form.tiers.splice(index, 1);
            },

            setPreview(platform) {
                this.previewPlatform = platform;
                localStorage.setItem('uppy_preview_platform', platform);
            },

            previewExpirationText() {
                if (this.expirationMode === 'unlimited') return 'Sin vencimiento';
                if (this.expirationMode === 'fixed_term') return this.form.expires_at || 'Sin fecha';
                if (this.expirationMode === 'relative_after_issue') {
                    return `${this.form.expires_after_value || 1} ${this.form.expires_after_unit || 'days'}`;
                }
                return 'Sin vencimiento';
            },

            tierSummary() {
                if (!this.form.tiers.length) return { name: 'Bronce', percentage: 0 };
                return {
                    name: this.form.tiers[0].name || 'Bronce',
                    percentage: this.form.tiers[0].percentage || 0,
                };
            }
        }"
    >
        {{-- Panel principal --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Editar tarjeta</h2>
                    <p class="text-sm text-gray-500">
                        Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                    </p>
                </div>

                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                    Draft
                </span>
            </div>

            <div class="px-6 pt-3">
                <div id="autosave-indicator" class="text-xs text-gray-400">
                    Guardado
                </div>
            </div>

            <div class="px-6 pt-4 pb-3 border-b border-gray-200">
                <div class="grid grid-cols-4 gap-2 text-xs sm:text-sm">
                    <div class="relative pb-2 text-blue-600 border-b-2 border-blue-600">
                        Información
                    </div>

                    <a href="{{ route('cards.wizard.step2', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Diseño
                    </a>

                    <a href="{{ route('cards.wizard.step3', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Detalles
                    </a>

                    <a href="{{ route('cards.wizard.step4', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Notificaciones
                    </a>
                </div>
            </div>

            <div class="px-6 py-5 space-y-6">
                @if(session('success'))
                    <div class="px-4 py-3 text-sm text-green-800 bg-green-50 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="px-4 py-3 text-sm text-red-800 bg-red-50 rounded-lg">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">
                        Nombre de la tarjeta
                    </label>

                    <input
                        type="text"
                        x-model="form.name"
                        @input="changed()"
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                        placeholder="Ej. Tarjeta de Lealtad"
                    >
                </div>

                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Vigencia</h3>

                    <div class="space-y-4">
                        <label class="flex items-center gap-3 text-sm text-gray-800">
                            <input
                                type="radio"
                                value="unlimited"
                                x-model="expirationMode"
                                @change="changed()"
                                class="border-gray-300"
                            >
                            Ilimitado
                        </label>

                        <label class="flex items-center gap-3 text-sm text-gray-800">
                            <input
                                type="radio"
                                value="fixed_term"
                                x-model="expirationMode"
                                @change="changed()"
                                class="border-gray-300"
                            >
                            Término fijo
                        </label>

                        <div x-show="expirationMode === 'fixed_term'" x-cloak class="ml-7">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Fecha de término</label>
                            <input
                                type="date"
                                x-model="form.expires_at"
                                @input="changed()"
                                class="w-full md:w-72 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>

                        <label class="flex items-center gap-3 text-sm text-gray-800">
                            <input
                                type="radio"
                                value="relative_after_issue"
                                x-model="expirationMode"
                                @change="changed()"
                                class="border-gray-300"
                            >
                            Plazo fijo después de emisión
                        </label>

                        <div x-show="expirationMode === 'relative_after_issue'" x-cloak class="ml-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Cantidad</label>
                                <input
                                    type="number"
                                    min="1"
                                    x-model="form.expires_after_value"
                                    @input="changed()"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                >
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Unidad</label>
                                <select
                                    x-model="form.expires_after_unit"
                                    @change="changed()"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                >
                                    <option value="days">Días</option>
                                    <option value="weeks">Semanas</option>
                                    <option value="months">Meses</option>
                                    <option value="years">Años</option>
                                </select>
                            </div>
                        </div>

                        <label class="flex items-center gap-3 text-sm text-gray-800">
                            <input
                                type="checkbox"
                                x-model="form.is_unlimited"
                                @change="changed()"
                                class="rounded border-gray-300"
                            >
                            Tarjetas ilimitadas
                        </label>
                    </div>
                </div>

                @if($typeCode === 'stamps')
                    <div class="border-t border-gray-200 pt-5 space-y-5">
                        <h3 class="text-base font-semibold text-gray-900">Configuración de sellos</h3>

                        <div>
                            <label class="block mb-3 text-sm font-medium text-gray-800">Tipo de programa</label>

                            <div class="space-y-3">
                                <label class="flex items-start gap-3 text-sm text-gray-800">
                                    <input type="radio" value="purchase" x-model="stampsEarningMode" @change="changed()" class="mt-1 border-gray-300">
                                    <div>
                                        <p class="font-medium">Gasto</p>
                                        <p class="text-xs text-gray-500">Entrega sellos según el gasto del cliente.</p>
                                    </div>
                                </label>

                                <div x-show="stampsEarningMode === 'purchase'" x-cloak class="ml-7">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Cantidad de gasto</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        x-model="form.purchase_amount_required"
                                        @input="changed()"
                                        class="w-full md:w-72 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                    >
                                </div>

                                <label class="flex items-start gap-3 text-sm text-gray-800">
                                    <input type="radio" value="visit" x-model="stampsEarningMode" @change="changed()" class="mt-1 border-gray-300">
                                    <div>
                                        <p class="font-medium">Visita</p>
                                        <p class="text-xs text-gray-500">Entrega sellos en cada visita.</p>
                                    </div>
                                </label>

                                <div x-show="stampsEarningMode === 'visit'" x-cloak class="ml-7">
                                    <label class="flex items-center gap-3 text-sm text-gray-800">
                                        <input
                                            type="checkbox"
                                            x-model="form.daily_limit_enabled"
                                            @change="changed()"
                                            class="rounded border-gray-300"
                                        >
                                        Limitar a una visita por día
                                    </label>
                                </div>

                                <label class="flex items-start gap-3 text-sm text-gray-800">
                                    <input type="radio" value="event" x-model="stampsEarningMode" @change="changed()" class="mt-1 border-gray-300">
                                    <div>
                                        <p class="font-medium">Evento</p>
                                        <p class="text-xs text-gray-500">Entrega sellos cuando el cliente realice una acción específica.</p>
                                    </div>
                                </label>

                                <div x-show="stampsEarningMode === 'event'" x-cloak class="ml-7">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Descripción del evento</label>
                                    <input
                                        type="text"
                                        x-model="form.event_description"
                                        @input="changed()"
                                        class="w-full md:w-96 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                        placeholder="Ej. Compra de 2 productos"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-800">Cantidad de sellos</label>
                                <input
                                    type="number"
                                    min="1"
                                    max="21"
                                    x-model="form.stamps_required"
                                    @input="changed()"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                >
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-800">Sellos al descargar</label>
                                <input
                                    type="number"
                                    min="0"
                                    x-model="form.stamps_on_signup"
                                    @input="changed()"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                >
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-800">Recompensa</label>
                                <input
                                    type="text"
                                    x-model="form.reward_text"
                                    @input="changed()"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                    placeholder="Ej. Un café gratis"
                                >
                            </div>
                        </div>
                    </div>
                @endif

                @if($typeCode === 'coupon')
                    <div class="border-t border-gray-200 pt-5 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900">Configuración de cupón</h3>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Válido por</label>
                            <input
                                type="text"
                                x-model="form.coupon_valid_for"
                                @input="changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                placeholder="Ej. Un café"
                            >
                        </div>

                        <label class="flex items-center gap-3 text-sm text-gray-800">
                            <input
                                type="checkbox"
                                x-model="form.coupon_replace_on_redeem"
                                @change="changed()"
                                class="rounded border-gray-300"
                            >
                            Reemplazar al canjear
                        </label>
                    </div>
                @endif

                @if(in_array($typeCode, ['cashback', 'discount_levels']))
                    <div class="border-t border-gray-200 pt-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ $typeCode === 'cashback' ? 'Niveles de reembolso' : 'Niveles de descuento' }}
                            </h3>

                            <button
                                type="button"
                                @click="addTier()"
                                class="inline-flex items-center justify-center px-3 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition"
                            >
                                + Agregar nivel
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(tier, index) in form.tiers" :key="index">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start border border-gray-200 rounded-xl p-4">
                                    <div class="md:col-span-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Nombre</label>
                                        <input
                                            type="text"
                                            x-model="tier.name"
                                            @input="changed()"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                            placeholder="Ej. Bronce"
                                        >
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Monto mínimo</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="tier.threshold_amount"
                                            @input="changed()"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                            placeholder="1000"
                                        >
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Porcentaje</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="tier.percentage"
                                            @input="changed()"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                            placeholder="5"
                                        >
                                    </div>

                                    <div class="md:col-span-2 flex md:justify-end">
                                        <button
                                            type="button"
                                            @click="removeTier(index); changed()"
                                            class="mt-0 md:mt-7 inline-flex items-center justify-center px-3 py-2 text-sm text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @endif

                @if($typeCode === 'giftcard')
                    <div class="border-t border-gray-200 pt-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Configuración gift card</h3>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Saldo inicial</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                x-model="form.giftcard_initial_balance"
                                @input="changed()"
                                class="w-full md:w-72 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>
                    </div>
                @endif

                @if($typeCode === 'points')
                    <div class="border-t border-gray-200 pt-5 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900">Configuración de puntos</h3>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Puntos por compra</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                x-model="form.points_per_purchase"
                                @input="changed()"
                                class="w-full md:w-72 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Recompensa</label>
                            <input
                                type="text"
                                x-model="form.points_reward_text"
                                @input="changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                placeholder="Ej. 100 puntos = un regalo"
                            >
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-end pt-3 border-t border-gray-200">
                    <a href="{{ route('cards.wizard.step2', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Siguiente
                    </a>
                </div>
            </div>
        </div>

        {{-- Panel lateral con preview --}}
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
                                                    <p class="text-[15px] font-semibold leading-tight truncate" x-text="form.name || '{{ $displayName }}'"></p>
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <p class="text-[10px] opacity-45">Estado</p>
                                                <p class="text-[10px]">{{ ucfirst($card->status) }}</p>
                                            </div>
                                        </div>

                                        @unless($supportsStamps)
                                            <div class="mt-4 h-28 rounded-[1.1rem] bg-white/70 overflow-hidden border border-white/70">
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
                                                    <p class="text-[10px] opacity-60">
                                                        <span x-text="form.stamps_on_signup"></span>/<span x-text="form.stamps_required"></span>
                                                    </p>
                                                </div>

                                                <div class="grid grid-cols-5 gap-2">
                                                    <template x-for="index in Math.max(5, Math.min(10, Number(form.stamps_required || 10)))" :key="'ios-step1-'+index">
                                                        <div
                                                            class="h-8.5 w-8.5 rounded-full flex items-center justify-center text-sm font-bold shadow-sm overflow-hidden"
                                                            :style="index <= Number(form.stamps_on_signup || 0)
                                                                ? `background:${design.active_color}; color:white;`
                                                                : `background:${design.inactive_color}; color:${design.text_color}; opacity:0.88;`"
                                                        >
                                                            <template x-if="index <= Number(form.stamps_on_signup || 0) && stampActiveImageUrl">
                                                                <img :src="stampActiveImageUrl" class="w-full h-full object-cover">
                                                            </template>

                                                            <template x-if="!(index <= Number(form.stamps_on_signup || 0) && stampActiveImageUrl) && !(index > Number(form.stamps_on_signup || 0) && stampInactiveImageUrl)">
                                                                <span x-text="index <= Number(form.stamps_on_signup || 0) ? '★' : '●'"></span>
                                                            </template>

                                                            <template x-if="index > Number(form.stamps_on_signup || 0) && stampInactiveImageUrl">
                                                                <img :src="stampInactiveImageUrl" class="w-full h-full object-cover">
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>

                                                <div class="mt-3 text-[11px]">
                                                    <span class="opacity-60">Recompensa:</span>
                                                    <span class="font-medium" x-text="form.reward_text || 'Sin recompensa'"></span>
                                                </div>
                                            </div>
                                        @endif

                                        @if($typeCode === 'coupon')
                                            <div class="mt-4 rounded-[1.15rem] bg-white/65 border border-white/70 px-3 py-3">
                                                <p class="text-[10px] opacity-60 uppercase">Válido por</p>
                                                <p class="text-[13px] font-semibold mt-1" x-text="form.coupon_valid_for || 'Sin definir'"></p>
                                            </div>
                                        @endif

                                        @if($typeCode === 'giftcard')
                                            <div class="mt-4 rounded-[1.15rem] bg-white/65 border border-white/70 px-3 py-3">
                                                <p class="text-[10px] opacity-60 uppercase">Saldo inicial</p>
                                                <p class="text-[24px] font-bold mt-1">$<span x-text="form.giftcard_initial_balance || 0"></span></p>
                                            </div>
                                        @endif

                                        @if($typeCode === 'points')
                                            <div class="mt-4 rounded-[1.15rem] bg-white/65 border border-white/70 px-3 py-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-[10px] opacity-60 uppercase">Puntos</p>
                                                        <p class="text-[24px] font-bold mt-1" x-text="form.points_per_purchase || 0"></p>
                                                    </div>
                                                    <div class="text-right text-[10px] opacity-60">
                                                        <p x-text="previewExpirationText()"></p>
                                                    </div>
                                                </div>

                                                <div class="mt-3 text-[11px]">
                                                    <span class="opacity-60">Recompensa:</span>
                                                    <span class="font-medium" x-text="form.points_reward_text || 'Sin recompensa'"></span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(in_array($typeCode, ['cashback', 'discount_levels']))
                                            <div class="mt-4 rounded-[1.15rem] bg-white/65 border border-white/70 px-3 py-3">
                                                <div class="flex items-end justify-between gap-3">
                                                    <div>
                                                        <p class="text-[10px] opacity-60 uppercase">
                                                            {{ $typeCode === 'cashback' ? 'Reembolso' : 'Descuento' }}
                                                        </p>
                                                        <p class="text-[24px] font-bold mt-1">
                                                            <span x-text="tierSummary().percentage || 0"></span>%
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-[10px] opacity-60 uppercase">Nivel</p>
                                                        <p class="text-[12px] font-semibold" x-text="tierSummary().name || 'Bronce'"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

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

                                        <div class="mt-4 text-[11px] opacity-65">
                                            Vigencia: <span x-text="previewExpirationText()"></span>
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
                                                <p class="text-[15px] font-semibold leading-tight truncate text-white" x-text="form.name || '{{ $displayName }}'"></p>
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
                                        <div class="h-24 rounded-[0.95rem] overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400 border border-gray-100">
                                            <template x-if="mainImageUrl">
                                                <img :src="mainImageUrl" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!mainImageUrl">
                                                <span>Imagen principal</span>
                                            </template>
                                        </div>
                                    @endunless

                                    @if($supportsStamps)
                                        <div class="mt-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-[11px] font-medium text-gray-500">Progreso</p>
                                                <p class="text-[10px] text-gray-400">
                                                    <span x-text="form.stamps_on_signup"></span>/<span x-text="form.stamps_required"></span>
                                                </p>
                                            </div>

                                            <div class="grid grid-cols-5 gap-2">
                                                <template x-for="index in Math.max(5, Math.min(10, Number(form.stamps_required || 10)))" :key="'android-step1-'+index">
                                                    <div
                                                        class="h-8.5 w-8.5 rounded-lg flex items-center justify-center text-sm font-bold border overflow-hidden"
                                                        :style="index <= Number(form.stamps_on_signup || 0)
                                                            ? `background:${design.active_color}; color:white; border-color:${design.active_color};`
                                                            : `background:white; color:${design.inactive_color}; border-color:${design.inactive_color};`"
                                                    >
                                                        <template x-if="index <= Number(form.stamps_on_signup || 0) && stampActiveImageUrl">
                                                            <img :src="stampActiveImageUrl" class="w-full h-full object-cover">
                                                        </template>

                                                        <template x-if="!(index <= Number(form.stamps_on_signup || 0) && stampActiveImageUrl) && !(index > Number(form.stamps_on_signup || 0) && stampInactiveImageUrl)">
                                                            <span x-text="index <= Number(form.stamps_on_signup || 0) ? '★' : '●'"></span>
                                                        </template>

                                                        <template x-if="index > Number(form.stamps_on_signup || 0) && stampInactiveImageUrl">
                                                            <img :src="stampInactiveImageUrl" class="w-full h-full object-cover">
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="mt-3 text-[11px] text-gray-600">
                                                <span class="text-gray-400">Recompensa:</span>
                                                <span class="font-medium" x-text="form.reward_text || 'Sin recompensa'"></span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($typeCode === 'coupon')
                                        <div class="mt-4 rounded-[0.95rem] bg-[#f8fafc] p-4 border border-gray-200">
                                            <p class="text-[10px] text-gray-400 uppercase">Válido por</p>
                                            <p class="mt-1 text-[13px] font-semibold text-gray-800" x-text="form.coupon_valid_for || 'Sin definir'"></p>
                                        </div>
                                    @endif

                                    @if($typeCode === 'giftcard')
                                        <div class="mt-4 rounded-[0.95rem] bg-[#f8fafc] p-4 border border-gray-200">
                                            <p class="text-[10px] text-gray-400 uppercase">Saldo inicial</p>
                                            <p class="mt-1 text-[26px] font-bold text-gray-900">$<span x-text="form.giftcard_initial_balance || 0"></span></p>
                                        </div>
                                    @endif

                                    @if($typeCode === 'points')
                                        <div class="mt-4 rounded-[0.95rem] bg-[#f8fafc] p-4 border border-gray-200">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Puntos</p>
                                                    <p class="mt-1 text-[26px] font-bold text-blue-600" x-text="form.points_per_purchase || 0"></p>
                                                </div>
                                                <div class="text-right text-[10px] text-gray-400">
                                                    <p x-text="previewExpirationText()"></p>
                                                </div>
                                            </div>

                                            <div class="mt-3 text-[11px] text-gray-600">
                                                <span class="text-gray-400">Recompensa:</span>
                                                <span class="font-medium" x-text="form.points_reward_text || 'Sin recompensa'"></span>
                                            </div>
                                        </div>
                                    @endif

                                    @if(in_array($typeCode, ['cashback', 'discount_levels']))
                                        <div class="mt-4 rounded-[0.95rem] bg-[#f8fafc] p-4 border border-gray-200">
                                            <div class="flex items-end justify-between gap-3">
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">
                                                        {{ $typeCode === 'cashback' ? 'Reembolso' : 'Descuento' }}
                                                    </p>
                                                    <p class="mt-1 text-[26px] font-bold text-blue-600">
                                                        <span x-text="tierSummary().percentage || 0"></span>%
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-[10px] text-gray-400 uppercase">Nivel</p>
                                                    <p class="text-[12px] font-semibold text-gray-700" x-text="tierSummary().name || 'Bronce'"></p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

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

                                    <div class="mt-4 text-[11px] text-gray-400">
                                        Vigencia: <span x-text="previewExpirationText()"></span>
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
                <p><span class="font-medium text-gray-700">Nombre:</span> <span x-text="form.name || '{{ $displayName }}'"></span></p>
                <p><span class="font-medium text-gray-700">Tipo:</span> {{ $card->type->code }}</p>
                <p><span class="font-medium text-gray-700">Preview:</span> <span x-text="previewPlatform"></span></p>
                <p><span class="font-medium text-gray-700">Código:</span> {{ $codeType }}</p>
            </div>
        </div>
    </div>
@endsection
