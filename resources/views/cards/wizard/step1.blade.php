@extends('layouts.app')

@section('content')
    @php
        $typeCode = $card->type->code;
        $expirationMode = old('expiration_mode', $card->config('general.expiration_mode', 'unlimited'));
        $earningMode = old('stamps_earning_mode', $card->config('stamps.earning_mode', 'visit'));
    @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3"
         x-data="{
            expirationMode: '{{ $expirationMode }}',
            earningMode: '{{ $earningMode }}'
         }">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Editar tarjeta</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                    </p>
                </div>

                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                    Draft
                </span>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-900 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="px-3 py-1 rounded-full bg-blue-600 text-white">1. Información</span>
                        <a href="{{ route('cards.wizard.step2', $card) }}"
                           class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">2. Diseño</a>
                        <a href="{{ route('cards.wizard.step3', $card) }}"
                           class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">3. Detalles</a>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">4. Notificaciones</span>
                    </div>
                </div>

                <form action="{{ route('cards.wizard.step1.save', $card) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nombre de la tarjeta
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $card->name) }}"
                                class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($typeCode === 'stamps')
                            <div class="space-y-6">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Tipo de programa
                                    </label>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="flex items-start gap-3">
                                                <input type="radio" name="stamps_earning_mode" value="purchase" x-model="earningMode">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Gasto</span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Entrega sellos según el gasto del cliente.</p>
                                                </div>
                                            </label>

                                            <div x-show="earningMode === 'purchase'" x-cloak class="ml-6 mt-3">
                                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                    Cantidad de gasto
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    name="purchase_amount_required"
                                                    value="{{ old('purchase_amount_required', $card->config('stamps.purchase_amount_required', 100)) }}"
                                                    class="w-full md:w-1/2 p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                >
                                            </div>
                                        </div>

                                        <div>
                                            <label class="flex items-start gap-3">
                                                <input type="radio" name="stamps_earning_mode" value="visit" x-model="earningMode">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Visita</span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Entrega sellos en cada visita.</p>
                                                </div>
                                            </label>

                                            <div x-show="earningMode === 'visit'" x-cloak class="ml-6 mt-3">
                                                <label class="flex items-center gap-3">
                                                    <input
                                                        type="checkbox"
                                                        name="daily_limit_enabled"
                                                        value="1"
                                                        {{ old('daily_limit_enabled', $card->config('stamps.daily_limit_enabled', false)) ? 'checked' : '' }}
                                                    >
                                                    <span class="text-sm text-gray-900 dark:text-white">
                                                        Limitar a una visita por día
                                                    </span>
                                                </label>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="flex items-start gap-3">
                                                <input type="radio" name="stamps_earning_mode" value="event" x-model="earningMode">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Evento</span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Entrega sellos cuando el cliente realice una compra o acción específica.</p>
                                                </div>
                                            </label>

                                            <div x-show="earningMode === 'event'" x-cloak class="ml-6 mt-3">
                                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                    Descripción del evento
                                                </label>
                                                <input
                                                    type="text"
                                                    name="event_description"
                                                    value="{{ old('event_description', $card->config('stamps.event_description')) }}"
                                                    placeholder="Ej. Compra de 2 productos"
                                                    class="w-full md:w-1/2 p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    @error('stamps_earning_mode')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Cantidad de sellos
                                    </label>
                                    <input
                                        type="number"
                                        name="stamps_required"
                                        min="1"
                                        max="21"
                                        value="{{ old('stamps_required', $card->config('stamps.required', 10)) }}"
                                        class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Sellos al descargar
                                    </label>
                                    <input
                                        type="number"
                                        name="stamps_on_signup"
                                        min="0"
                                        value="{{ old('stamps_on_signup', $card->config('stamps.on_signup', 0)) }}"
                                        class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Recompensa
                                    </label>
                                    <input
                                        type="text"
                                        name="reward_text"
                                        value="{{ old('reward_text', $card->config('stamps.reward_text')) }}"
                                        placeholder="Ej. Un café gratis"
                                        class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                            </div>
                        @endif

                        @if($typeCode === 'coupon')
                            <div class="space-y-6">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Válido por
                                    </label>
                                    <input
                                        type="text"
                                        name="coupon_valid_for"
                                        value="{{ old('coupon_valid_for', $card->config('coupon.valid_for')) }}"
                                        placeholder="Ej. Un café"
                                        class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>

                                <div>
                                    <label class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="coupon_replace_on_redeem"
                                            value="1"
                                            {{ old('coupon_replace_on_redeem', $card->config('coupon.replace_on_redeem', false)) ? 'checked' : '' }}
                                        >
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            Reemplazar al canjear
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        @if(in_array($typeCode, ['cashback', 'discount_levels']))
                            <div class="space-y-4">
                                @php
                                    $oldTiers = old('tiers');
                                    $existingTiers = $card->tiers->map(fn($tier) => [
                                        'name' => $tier->name,
                                        'threshold_amount' => $tier->threshold_amount,
                                        'percentage' => $tier->percentage,
                                    ])->toArray();

                                    $tiers = $oldTiers ?? (count($existingTiers) ? $existingTiers : [
                                        ['name' => 'Bronce', 'threshold_amount' => 0, 'percentage' => 1],
                                        ['name' => 'Plata', 'threshold_amount' => 1000, 'percentage' => 3],
                                        ['name' => 'Oro', 'threshold_amount' => 5000, 'percentage' => 5],
                                    ]);
                                @endphp

                                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $typeCode === 'cashback' ? 'Niveles de reembolso' : 'Niveles de descuento' }}
                                </label>

                                <div class="space-y-3">
                                    @foreach($tiers as $index => $tier)
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <input type="text" name="tiers[{{ $index }}][name]" value="{{ $tier['name'] ?? '' }}" placeholder="Ej. Bronce" class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <input type="number" step="0.01" name="tiers[{{ $index }}][threshold_amount]" value="{{ $tier['threshold_amount'] ?? '' }}" placeholder="Gasto para alcanzar" class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <input type="number" step="0.01" name="tiers[{{ $index }}][percentage]" value="{{ $tier['percentage'] ?? '' }}" placeholder="Porcentaje" class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($typeCode === 'giftcard')
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Saldo inicial
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="giftcard_initial_balance"
                                    value="{{ old('giftcard_initial_balance', $card->config('giftcard.initial_balance', 0)) }}"
                                    class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                            </div>
                        @endif

                        @if($typeCode === 'points')
                            <div class="space-y-6">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Puntos por compra
                                    </label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="points_per_purchase"
                                        value="{{ old('points_per_purchase', $card->config('points.per_purchase', 1)) }}"
                                        class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Recompensa
                                    </label>
                                    <input
                                        type="text"
                                        name="reward_text"
                                        value="{{ old('reward_text', $card->config('points.reward_text')) }}"
                                        class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Información general
                            </h3>

                            <div class="mt-4 space-y-6">
                                <div>
                                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                        Vigencia de la tarjeta
                                    </label>

                                    <div class="space-y-3">
                                        <label class="flex items-center gap-3">
                                            <input type="radio" name="expiration_mode" value="unlimited" x-model="expirationMode">
                                            <span class="text-sm text-gray-900 dark:text-white">Ilimitado</span>
                                        </label>

                                        <label class="flex items-center gap-3">
                                            <input type="radio" name="expiration_mode" value="fixed_term" x-model="expirationMode">
                                            <span class="text-sm text-gray-900 dark:text-white">Término fijo</span>
                                        </label>

                                        <div x-show="expirationMode === 'fixed_term'" x-cloak class="ml-6">
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                Fecha de término
                                            </label>
                                            <input
                                                type="date"
                                                name="expires_at"
                                                value="{{ old('expires_at', optional($card->ends_at)->format('Y-m-d') ?? $card->config('general.expires_at')) }}"
                                                class="w-full md:w-1/2 p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            >
                                        </div>

                                        <label class="flex items-center gap-3">
                                            <input type="radio" name="expiration_mode" value="relative_after_issue" x-model="expirationMode">
                                            <span class="text-sm text-gray-900 dark:text-white">Plazo fijo después de emisión</span>
                                        </label>

                                        <div x-show="expirationMode === 'relative_after_issue'" x-cloak class="ml-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                    Cantidad de tiempo
                                                </label>
                                                <input
                                                    type="number"
                                                    min="1"
                                                    name="expires_after_value"
                                                    value="{{ old('expires_after_value', $card->config('general.expires_after_value', 1)) }}"
                                                    class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                >
                                            </div>

                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                    Unidad
                                                </label>
                                                @php $expiresAfterUnit = old('expires_after_unit', $card->config('general.expires_after_unit', 'days')); @endphp
                                                <select
                                                    name="expires_after_unit"
                                                    class="w-full p-3 text-sm border border-gray-300 rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                >
                                                    <option value="days" {{ $expiresAfterUnit === 'days' ? 'selected' : '' }}>Días</option>
                                                    <option value="weeks" {{ $expiresAfterUnit === 'weeks' ? 'selected' : '' }}>Semanas</option>
                                                    <option value="months" {{ $expiresAfterUnit === 'months' ? 'selected' : '' }}>Meses</option>
                                                    <option value="years" {{ $expiresAfterUnit === 'years' ? 'selected' : '' }}>Años</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="is_unlimited"
                                            value="1"
                                            {{ old('is_unlimited', $card->config('general.is_unlimited', true)) ? 'checked' : '' }}
                                        >
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            Tarjetas ilimitadas
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div></div>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700"
                            >
                                Guardar paso 1
                            </button>

                            <a href="{{ route('cards.wizard.step2', $card) }}"
                               class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                Siguiente
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vista previa</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Preview inicial del borrador.
            </p>

            <div class="mt-6 flex justify-center">
                <div class="w-64 rounded-[2rem] border-8 border-gray-900 bg-gray-100 p-4 shadow-xl">
                    <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ $card->type->name }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ old('name', $card->name) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400">Estado</p>
                                <p class="text-[10px] text-gray-500">{{ ucfirst($card->status) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                            Imagen principal
                        </div>

                        <div class="mt-4 text-xs text-gray-500 space-y-1">
                            @if($typeCode === 'stamps')
                                <p>Recompensa: {{ old('reward_text', $card->config('stamps.reward_text', '—')) }}</p>
                                <p>Sellos: {{ old('stamps_required', $card->config('stamps.required', '—')) }}</p>
                            @endif

                            @if($typeCode === 'coupon')
                                <p>Válido por: {{ old('coupon_valid_for', $card->config('coupon.valid_for', '—')) }}</p>
                            @endif
                        </div>

                        <div class="mt-4">
                            <p class="text-xs text-gray-500">Código</p>
                            <div class="mt-3 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                                {{ strtoupper($card->code_type) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                <p><strong>ID:</strong> {{ $card->id }}</p>
                <p><strong>Creada:</strong> {{ $card->created_at?->format('d/m/Y H:i') }}</p>
                <p><strong>Tipo:</strong> {{ $card->type->code }}</p>
            </div>
        </div>
    </div>
@endsection
