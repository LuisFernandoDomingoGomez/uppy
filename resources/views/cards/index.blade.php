@extends('layouts.app')

@section('content')
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tarjetas</h2>
                <p class="text-sm text-gray-500">Administra las tarjetas de lealtad de tu negocio</p>
            </div>

            <a href="{{ route('cards.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">
                + Agregar
            </a>
        </div>

        <div class="p-6">
            @if ($cards->isEmpty())
                <div class="py-20 text-center">
                    <h3 class="text-3xl font-bold text-gray-900">Aún no hay tarjetas</h3>
                    <p class="mt-3 text-gray-500">Crea tu primera tarjeta para comenzar.</p>
                    <a href="{{ route('cards.create') }}"
                       class="inline-block mt-6 px-6 py-3 text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                        Crear primera tarjeta
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                    @foreach ($cards as $card)
                        @php
                            $settings = is_array($card->settings_json) ? $card->settings_json : [];

                            $rawTypeSlug = strtolower((string) ($card->type->slug ?? ''));
                            $rawTypeName = strtolower((string) ($card->type->name ?? ''));
                            $rawTypeCode = strtolower((string) ($card->type->code ?? ''));

                            if (
                                str_contains($rawTypeSlug, 'stamp') ||
                                str_contains($rawTypeName, 'stamp') ||
                                str_contains($rawTypeName, 'sello') ||
                                str_contains($rawTypeCode, 'stamp')
                            ) {
                                $typeSlug = 'stamps';
                            } elseif (
                                str_contains($rawTypeSlug, 'gift') ||
                                str_contains($rawTypeName, 'regalo') ||
                                str_contains($rawTypeName, 'gift') ||
                                str_contains($rawTypeCode, 'gift')
                            ) {
                                $typeSlug = 'giftcard';
                            } elseif (
                                str_contains($rawTypeSlug, 'coupon') ||
                                str_contains($rawTypeName, 'cup') ||
                                str_contains($rawTypeCode, 'coupon')
                            ) {
                                $typeSlug = 'coupon';
                            } elseif (
                                str_contains($rawTypeSlug, 'cashback') ||
                                str_contains($rawTypeName, 'cashback') ||
                                str_contains($rawTypeCode, 'cashback')
                            ) {
                                $typeSlug = 'cashback';
                            } elseif (
                                str_contains($rawTypeSlug, 'discount') ||
                                str_contains($rawTypeName, 'descuento') ||
                                str_contains($rawTypeCode, 'discount')
                            ) {
                                $typeSlug = 'discount_levels';
                            } elseif (
                                str_contains($rawTypeSlug, 'point') ||
                                str_contains($rawTypeName, 'punto') ||
                                str_contains($rawTypeCode, 'point')
                            ) {
                                $typeSlug = 'points';
                            } else {
                                $typeSlug = $rawTypeCode ?: ($rawTypeSlug ?: $rawTypeName);
                            }

                            $typeName = $card->type->name ?? ucfirst($typeSlug);
                            $displayName = $settings['display_name'] ?? $card->name ?? 'Tarjeta sin nombre';
                            $createdDate = $card->created_at?->format('d/m/Y');

                            $statusClasses = match ($card->status) {
                                'active' => 'bg-emerald-100 text-emerald-700',
                                'inactive' => 'bg-gray-100 text-gray-600',
                                default => 'bg-amber-100 text-amber-700',
                            };

                            $statusLabel = match ($card->status) {
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                default => 'Draft',
                            };
                        @endphp

                        <a href="{{ route('cards.show', $card) }}"
                           class="group block h-full rounded-[24px] border border-gray-200 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-full flex-col">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-gray-400">
                                            {{ $typeName }}
                                        </p>
                                        <h3 class="mt-1 text-[17px] leading-tight font-bold text-gray-900 line-clamp-2 min-h-[42px]">
                                            {{ $displayName }}
                                        </h3>
                                    </div>

                                    <span class="shrink-0 inline-flex items-center rounded-full px-3 py-1 text-[10px] font-medium {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="mt-4">
                                    @include('cards.partials.index-preview', [
                                        'card' => $card,
                                        'typeSlug' => $typeSlug,
                                        'typeName' => $typeName,
                                        'displayName' => $displayName,
                                    ])
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3 text-[11px] text-gray-500">
                                    <span class="shrink-0">{{ $createdDate }}</span>
                                    <span class="truncate text-right">{{ $card->slug }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
