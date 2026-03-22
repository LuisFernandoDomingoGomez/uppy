<div class="overflow-hidden rounded-[20px] border border-gray-200 bg-white shadow-[0_10px_24px_rgba(0,0,0,0.08)]">
    <div class="px-4 pt-4 pb-3"
         style="background: linear-gradient(135deg, {{ $backgroundColor }} 0%, {{ $activeColor }} 100%); color: white;">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if($logoSquareUrl)
                    <img src="{{ $logoSquareUrl }}" class="h-8 w-8 rounded-full object-cover border border-white/40 shadow-sm shrink-0 bg-white" alt="Logo">
                @elseif($logoHorizontalUrl)
                    <img src="{{ $logoHorizontalUrl }}" class="h-5 object-contain shrink-0 brightness-[1.15]" alt="Logo">
                @else
                    <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-semibold shrink-0">
                        U
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-[9px] text-white/75 truncate">{{ $typeName }}</p>
                    <p class="text-[12px] font-semibold leading-tight truncate text-white">{{ $displayName }}</p>
                </div>
            </div>

            <div class="text-right text-[9px] text-white/75 shrink-0">
                QR
            </div>
        </div>
    </div>

    <div class="px-4 py-4 bg-white">
        @unless($supportsStamps)
            <div class="h-14 rounded-[12px] overflow-hidden bg-gray-100 flex items-center justify-center text-[10px] text-gray-400 border border-gray-100">
                @if($mainImageUrl)
                    <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="Imagen principal">
                @else
                    <span>Imagen principal</span>
                @endif
            </div>
        @endunless

        @if($supportsStamps)
            <div class="mt-1">
                <p class="mb-2 text-[10px] font-medium text-gray-500">Progreso</p>
                <div class="grid grid-cols-5 gap-2">
                    @for($i = 1; $i <= $stampSlots; $i++)
                        @if($i <= $filledStampCount)
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center text-sm font-bold border overflow-hidden"
                                 style="background: {{ $activeColor }}; color: white; border-color: {{ $activeColor }};">
                                @if($stampActiveImageUrl)
                                    <img src="{{ $stampActiveImageUrl }}" class="w-full h-full object-cover" alt="Sello activo">
                                @else
                                    ★
                                @endif
                            </div>
                        @else
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center text-sm font-bold border overflow-hidden"
                                 style="background: white; color: {{ $inactiveColor }}; border-color: {{ $inactiveColor }};">
                                @if($stampInactiveImageUrl)
                                    <img src="{{ $stampInactiveImageUrl }}" class="w-full h-full object-cover" alt="Sello inactivo">
                                @else
                                    ●
                                @endif
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        @endif

        <div class="mt-3">
            @if($typeSlug === 'coupon')
                <div class="flex items-start justify-between gap-3 text-[9px] mb-3">
                    <div class="min-w-0">
                        <p class="text-gray-400 uppercase">Válido por</p>
                        <p class="text-[11px] font-semibold text-gray-800 truncate">{{ $couponValidFor }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 uppercase">Vencimiento</p>
                        <p class="text-[10px] text-gray-500">{{ $expiresLabel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'cashback')
                <div class="flex items-end justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="text-gray-400 uppercase">Reembolso</p>
                        <p class="text-[22px] font-bold leading-none text-blue-600">{{ $cashbackPercent }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 uppercase">Nivel</p>
                        <p class="text-[11px] font-semibold text-gray-700">{{ $cashbackLevel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'discount_levels')
                <div class="flex items-end justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="text-gray-400 uppercase">Descuento</p>
                        <p class="text-[22px] font-bold leading-none text-blue-600">{{ $discountPercent }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 uppercase">Nivel</p>
                        <p class="text-[11px] font-semibold text-gray-700">{{ $discountLevel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'giftcard')
                <div class="flex items-start justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="text-gray-400 uppercase">Saldo</p>
                        <p class="text-[22px] font-bold leading-none text-gray-900">${{ number_format($giftcardBalance, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 uppercase">Disponible</p>
                        <p class="text-[10px] text-gray-500">{{ $expiresLabel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'points')
                <div class="flex items-start justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="text-gray-400 uppercase">Puntos</p>
                        <p class="text-[22px] font-bold leading-none text-blue-600">{{ $pointsPreviewValue }}</p>
                    </div>
                    <div class="text-right">
                        @if($pointsPerPurchase)
                            <p class="text-[10px] text-gray-500">{{ $pointsPerPurchase }} pts / compra</p>
                        @endif
                        <p class="text-[10px] text-gray-500">{{ $expiresLabel }}</p>
                    </div>
                </div>

                <div class="rounded-[12px] bg-[#f8fafc] px-3 py-2 border border-gray-200 text-[10px] text-gray-600">
                    {{ $rewardText }}
                </div>
            @endif

            <div class="mt-3 rounded-[14px] bg-[#f8fafc] p-3 border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-medium text-gray-500">Código</p>
                    <div class="h-1.5 w-10 rounded-full" style="background: {{ $activeColor }}"></div>
                </div>

                @if($codeType === 'barcode')
                    <div class="text-center">
                        @if($barcodeSvg)
                            <img src="{{ $barcodeSvg }}" class="mx-auto h-10 object-contain" alt="Barcode">
                        @else
                            <div class="h-10 w-32 mx-auto rounded bg-white border border-gray-300"></div>
                        @endif
                        <p class="mt-2 text-[9px] tracking-[0.18em] uppercase text-gray-500 truncate">
                            {{ $barcodeLabel }}
                        </p>
                    </div>
                @else
                    <div class="flex justify-center">
                        @if($qrSvg)
                            <img src="{{ $qrSvg }}" class="w-16 h-16 object-contain" alt="QR">
                        @else
                            <div class="w-16 h-16 bg-white border border-gray-300 rounded"></div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
