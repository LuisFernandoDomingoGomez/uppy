@extends('layouts.app')

@section('content')
    @php
        $typeCode = $card->type->code;

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
    @endphp

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_320px]"
        x-data="{
            typeCode: @js($typeCode),
            expirationMode: @js($expirationMode),
            stampsEarningMode: @js($stampsEarningMode),

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
            }
        }"
    >
        {{-- Panel principal --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
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

            {{-- Indicador autosave --}}
            <div class="px-6 pt-3">
                <div id="autosave-indicator" class="text-xs text-gray-400">
                    Guardado
                </div>
            </div>

            {{-- Tabs --}}
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

                {{-- Nombre --}}
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

                {{-- Vigencia --}}
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

                {{-- Stamps --}}
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

                {{-- Coupon --}}
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

                {{-- Cashback / Discount levels --}}
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
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                            placeholder="5"
                                        >
                                    </div>

                                    <div class="md:col-span-2 flex md:justify-end">
                                        <button
                                            type="button"
                                            @click="removeTier(index)"
                                            class="mt-0 md:mt-7 inline-flex items-center justify-center px-3 py-2 text-sm text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="button"
                                @click="$nextTick(() => autosave.queue({ tiers: form.tiers, name: form.name, expiration_mode: expirationMode, expires_at: form.expires_at, expires_after_value: form.expires_after_value, expires_after_unit: form.expires_after_unit, is_unlimited: form.is_unlimited }))"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-orange-500 rounded-md hover:bg-orange-600 transition"
                            >
                                Guardar niveles
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Giftcard --}}
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

                {{-- Points --}}
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

                {{-- Navegación --}}
                <div class="flex items-center justify-end pt-3 border-t border-gray-200">
                    <a href="{{ route('cards.wizard.step2', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Siguiente
                    </a>
                </div>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900">Resumen</h3>
            <p class="mt-1 text-xs text-gray-500">Información principal de la tarjeta.</p>

            <div class="mt-5 space-y-4 text-xs">
                <div>
                    <p class="text-gray-400 mb-1">Nombre</p>
                    <p class="text-gray-900 font-medium" x-text="form.name || 'Sin nombre'"></p>
                </div>

                <div>
                    <p class="text-gray-400 mb-1">Tipo</p>
                    <p class="text-gray-900 font-medium">{{ $card->type->name }}</p>
                </div>

                <div>
                    <p class="text-gray-400 mb-1">Vigencia</p>
                    <template x-if="expirationMode === 'unlimited'">
                        <p class="text-gray-900 font-medium">Ilimitado</p>
                    </template>

                    <template x-if="expirationMode === 'fixed_term'">
                        <p class="text-gray-900 font-medium" x-text="form.expires_at || 'Sin fecha'"></p>
                    </template>

                    <template x-if="expirationMode === 'relative_after_issue'">
                        <p class="text-gray-900 font-medium">
                            <span x-text="form.expires_after_value"></span>
                            <span x-text="form.expires_after_unit"></span>
                        </p>
                    </template>
                </div>

                @if($typeCode === 'stamps')
                    <div>
                        <p class="text-gray-400 mb-1">Sellos requeridos</p>
                        <p class="text-gray-900 font-medium" x-text="form.stamps_required"></p>
                    </div>

                    <div>
                        <p class="text-gray-400 mb-1">Recompensa</p>
                        <p class="text-gray-900 font-medium" x-text="form.reward_text || 'Sin recompensa'"></p>
                    </div>
                @endif

                @if($typeCode === 'coupon')
                    <div>
                        <p class="text-gray-400 mb-1">Válido por</p>
                        <p class="text-gray-900 font-medium" x-text="form.coupon_valid_for || 'Sin definir'"></p>
                    </div>
                @endif

                @if($typeCode === 'giftcard')
                    <div>
                        <p class="text-gray-400 mb-1">Saldo inicial</p>
                        <p class="text-gray-900 font-medium" x-text="form.giftcard_initial_balance"></p>
                    </div>
                @endif

                @if($typeCode === 'points')
                    <div>
                        <p class="text-gray-400 mb-1">Puntos por compra</p>
                        <p class="text-gray-900 font-medium" x-text="form.points_per_purchase"></p>
                    </div>
                @endif

                <div class="pt-3 text-[11px] text-gray-400">
                    ID tarjeta: {{ $card->id }}
                </div>
            </div>
        </div>
    </div>
@endsection
