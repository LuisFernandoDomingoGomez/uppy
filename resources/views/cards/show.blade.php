@extends('layouts.app')

@section('content')
    @php
        use App\Support\CardPreviewCode;

        $design = $card->design;
        $notification = $card->notification;
        $previewPlatform = $design?->preview_platform ?? 'ios';
        $displayName = $card->settings_json['display_name'] ?? $card->name;
        $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
        $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
        $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;
        $backgroundColor = $design?->background_color ?? '#F3F4F6';
        $textColor = $design?->text_color ?? '#111827';
        $qrDataUri = CardPreviewCode::qrDataUri($card->id);
        $barcodeDataUri = CardPreviewCode::barcodeDataUri($card->id);
        $barcodeLabel = CardPreviewCode::barcodeLabel($card->id);
        $geoMessage = $notification?->settings_json['geo_message'] ?? null;
    @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $displayName }}</h2>
                    <p class="text-sm text-gray-500">
                        Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('cards.wizard.step1', $card) }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                        Editar
                    </a>

                    @if($card->status !== 'active')
                        <form action="{{ route('cards.publish', $card) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-xl hover:bg-emerald-700">
                                Activar
                            </button>
                        </form>
                    @else
                        <form action="{{ route('cards.pause', $card) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-xl hover:bg-amber-600">
                                Desactivar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Código</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ strtoupper($card->code_type) }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Plataforma</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ strtoupper($previewPlatform) }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Slug</p>
                        <p class="mt-1 font-semibold text-gray-900 truncate">{{ $card->slug }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-3">Enlaces</h3>
                        @forelse($card->links as $link)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-sm text-gray-700">{{ $link->type }}</span>
                                <span class="text-sm text-blue-600">{{ $link->label ?: $link->value }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Sin enlaces.</p>
                        @endforelse
                    </div>

                    <div class="p-4 rounded-2xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-3">Notificaciones</h3>

                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span>Cumpleaños</span>
                                <span class="{{ $notification?->birthday_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $notification?->birthday_enabled ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Última visita</span>
                                <span class="{{ $notification?->last_visit_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $notification?->last_visit_enabled ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Vencimiento</span>
                                <span class="{{ $notification?->expiration_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $notification?->expiration_enabled ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Compra</span>
                                <span class="{{ $notification?->purchase_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $notification?->purchase_enabled ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Recompensa</span>
                                <span class="{{ $notification?->reward_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $notification?->reward_enabled ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Geolocalización</span>
                                <span class="{{ $notification?->geo_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $notification?->geo_enabled ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-3">Términos y condiciones</h3>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $card->terms ?: 'Sin términos definidos.' }}</p>
                </div>

                @if($geoMessage)
                    <div class="p-4 rounded-2xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-3">Mensaje geolocalizado</h3>
                        <p class="text-sm text-gray-600">{{ $geoMessage }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900">Preview final</h3>
            <p class="mt-1 text-sm text-gray-500">Visual final de la tarjeta.</p>

            <div class="mt-6 flex justify-center">
                @if($previewPlatform === 'ios')
                    <div class="w-64 rounded-[2rem] border-8 border-gray-900 p-4 shadow-2xl" style="background: linear-gradient(180deg, {{ $backgroundColor }} 0%, #f3f4f6 100%)">
                        <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                        <div class="rounded-[1.4rem] bg-white p-4 shadow-lg min-h-[390px] border border-gray-100" style="color: {{ $textColor }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    @if($logoHorizontalUrl)
                                        <img src="{{ $logoHorizontalUrl }}" class="h-8 object-contain mb-2" alt="">
                                    @elseif($logoSquareUrl)
                                        <img src="{{ $logoSquareUrl }}" class="h-9 w-9 object-cover rounded-lg mb-2" alt="">
                                    @endif
                                    <p class="text-sm font-semibold leading-tight">{{ $displayName }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="text-[10px] opacity-60">Estado</p>
                                    <p class="text-[10px]">{{ ucfirst($card->status) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 h-24 rounded-2xl bg-gray-100 overflow-hidden flex items-center justify-center text-sm text-gray-400">
                                @if($mainImageUrl)
                                    <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                @else
                                    Imagen principal
                                @endif
                            </div>

                            <div class="mt-5">
                                <p class="text-xs opacity-60 mb-2">Código</p>
                                <div class="rounded-2xl bg-gray-50 p-3 flex flex-col items-center justify-center min-h-[110px] border border-gray-100">
                                    @if($card->code_type === 'qr')
                                        <img src="{{ $qrDataUri }}" class="w-24 h-24 object-contain" alt="">
                                    @else
                                        <img src="{{ $barcodeDataUri }}" class="mx-auto h-14 object-contain" alt="">
                                        <p class="mt-2 text-[10px] tracking-widest text-gray-500">{{ $barcodeLabel }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="w-72 rounded-[2.2rem] border-[7px] border-slate-900 p-3 shadow-2xl bg-slate-100">
                        <div class="mx-auto mb-3 h-5 w-20 rounded-full bg-slate-900"></div>

                        <div class="rounded-[1.6rem] overflow-hidden shadow-xl bg-white min-h-[410px] border border-gray-100">
                            <div class="px-4 pt-4 pb-3" style="background: linear-gradient(135deg, {{ $backgroundColor }} 0%, #ffffff 100%); color: {{ $textColor }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if($logoSquareUrl)
                                            <img src="{{ $logoSquareUrl }}" class="h-10 w-10 rounded-full object-cover shadow-sm" alt="">
                                        @elseif($logoHorizontalUrl)
                                            <img src="{{ $logoHorizontalUrl }}" class="h-7 object-contain" alt="">
                                        @endif

                                        <div>
                                            <p class="text-xs opacity-70">{{ $card->type->name }}</p>
                                            <p class="font-semibold leading-tight">{{ $displayName }}</p>
                                        </div>
                                    </div>

                                    <div class="text-right text-[10px] opacity-70">
                                        <p>Estado</p>
                                        <p>{{ ucfirst($card->status) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 pb-4">
                                <div class="mt-4 h-24 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400 shadow-sm">
                                    @if($mainImageUrl)
                                        <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        Imagen principal
                                    @endif
                                </div>

                                <div class="mt-5 rounded-2xl bg-gray-50 p-4 border border-gray-100 shadow-sm">
                                    <p class="text-xs opacity-60 mb-2">Código</p>
                                    @if($card->code_type === 'qr')
                                        <div class="flex justify-center">
                                            <img src="{{ $qrDataUri }}" class="w-28 h-28 object-contain" alt="">
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <img src="{{ $barcodeDataUri }}" class="mx-auto h-14 object-contain" alt="">
                                            <p class="mt-2 text-[10px] tracking-widest text-gray-500">{{ $barcodeLabel }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
