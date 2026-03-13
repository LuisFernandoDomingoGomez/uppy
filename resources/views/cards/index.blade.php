@extends('layouts.app')

@section('content')
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tarjetas</h2>
                <p class="text-sm text-gray-500">Administra las tarjetas de lealtad de tu negocio</p>
            </div>

            <a href="{{ route('cards.create') }}"
               class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                + Agregar
            </a>
        </div>

        <div class="p-6">
            @if($cards->isEmpty())
                <div class="py-20 text-center">
                    <h3 class="text-3xl font-bold text-gray-900">Aún no hay tarjetas</h3>
                    <p class="mt-3 text-gray-500">Crea tu primera tarjeta para comenzar.</p>
                    <a href="{{ route('cards.create') }}"
                       class="inline-block mt-6 px-6 py-3 text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                        Crear primera tarjeta
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($cards as $card)
                        @php
                            $design = $card->design;
                            $displayName = $card->settings_json['display_name'] ?? $card->name;
                            $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;
                            $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
                            $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
                            $backgroundColor = $design?->background_color ?? '#F3F4F6';
                            $textColor = $design?->text_color ?? '#111827';
                            $previewPlatform = $design?->preview_platform ?? 'ios';
                        @endphp

                        <a href="{{ route('cards.show', $card) }}"
                           class="block rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                        {{ $card->type->name }}
                                    </p>
                                    <h3 class="mt-1 text-xl font-bold text-gray-900">
                                        {{ $displayName }}
                                    </h3>
                                </div>

                                <span class="px-3 py-1 text-xs rounded-full
                                    {{ $card->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($card->status === 'inactive' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($card->status) }}
                                </span>
                            </div>

                            <div class="mt-4 rounded-2xl p-4 border border-gray-100 shadow-sm overflow-hidden"
                                 style="background: linear-gradient(135deg, {{ $backgroundColor }} 0%, #ffffff 100%); color: {{ $textColor }};">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        @if($logoSquareUrl)
                                            <img src="{{ $logoSquareUrl }}" class="h-8 w-8 rounded-full object-cover" alt="">
                                        @elseif($logoHorizontalUrl)
                                            <img src="{{ $logoHorizontalUrl }}" class="h-6 object-contain" alt="">
                                        @endif

                                        <div>
                                            <p class="text-xs opacity-70">{{ strtoupper($previewPlatform) }}</p>
                                            <p class="text-sm font-semibold leading-tight">{{ $displayName }}</p>
                                        </div>
                                    </div>

                                    <div class="text-right text-[10px] opacity-70">
                                        <p>{{ strtoupper($card->code_type) }}</p>
                                    </div>
                                </div>

                                <div class="mt-3 h-20 rounded-xl overflow-hidden bg-white/60 border border-white/50">
                                    @if($mainImageUrl)
                                        <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                                            Sin imagen
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $card->created_at?->format('d/m/Y') }}</span>
                                <span class="truncate max-w-[180px]">{{ $card->slug }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
