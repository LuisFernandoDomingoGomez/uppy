<div class="rounded-[20px] overflow-hidden border border-white/80 shadow-[0_10px_24px_rgba(0,0,0,0.08)]"
     style="background: linear-gradient(180deg, {{ $backgroundColor }} 0%, #ffffff 88%); color: {{ $textColor }};">
    <div class="px-4 pt-4 pb-3">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if($logoHorizontalUrl)
                    <img src="{{ $logoHorizontalUrl }}" class="h-5 object-contain shrink-0" alt="Logo">
                @elseif($logoSquareUrl)
                    <img src="{{ $logoSquareUrl }}" class="h-8 w-8 rounded-xl object-cover bg-white shadow-sm shrink-0" alt="Logo">
                @else
                    <div class="flex items-center justify-center h-8 w-8 rounded-xl bg-white text-[10px] font-semibold text-gray-700 shadow-sm shrink-0">
                        U
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-[9px] opacity-55 truncate">{{ $typeName }}</p>
                    <p class="text-[12px] font-semibold leading-tight truncate">{{ $displayName }}</p>
                </div>
            </div>

            <div class="text-right shrink-0">
                <p class="text-[9px] opacity-45">QR</p>
            </div>
        </div>

        @unless($supportsStamps)
            <div class="mt-3 h-16 rounded-[14px] bg-white/70 overflow-hidden border border-white/70">
                @if($mainImageUrl)
                    <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="Imagen principal">
                @else
                    <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">
                        Sin imagen
                    </div>
                @endif
            </div>
        @endunless

        @if($supportsStamps)
            <div class="mt-4 rounded-[16px] bg-white/65 border border-white/70 px-3 py-3">
                <p class="text-[11px] font-medium mb-3">Tarjeta de sellos</p>

                <div class="grid grid-cols-5 gap-2">
                    @for($i = 1; $i <= $stampSlots; $i++)
                        @if($i <= $filledStampCount)
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm overflow-hidden"
                                 style="background: {{ $activeColor }}; color: white;">
                                @if($stampActiveImageUrl)
                                    <img src="{{ $stampActiveImageUrl }}" class="w-full h-full object-cover" alt="Sello activo">
                                @else
                                    ★
                                @endif
                            </div>
                        @else
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm overflow-hidden"
                                 style="background: {{ $inactiveColor }}; color: {{ $textColor }}; opacity: .88;">
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
                        <p class="opacity-50 uppercase">Válido por</p>
                        <p class="font-semibold text-[11px] truncate">{{ $couponValidFor }}</p>
                    </div>
                    <div class="text-right">
                        <p class="opacity-50 uppercase">Vencimiento</p>
                        <p class="text-[10px]">{{ $expiresLabel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'cashback')
                <div class="flex items-end justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="opacity-50 uppercase">Reembolso</p>
                        <p class="text-[22px] font-bold leading-none">{{ $cashbackPercent }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="opacity-50 uppercase">Nivel</p>
                        <p class="text-[11px] font-semibold">{{ $cashbackLevel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'discount_levels')
                <div class="flex items-end justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="opacity-50 uppercase">Descuento</p>
                        <p class="text-[22px] font-bold leading-none">{{ $discountPercent }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="opacity-50 uppercase">Nivel</p>
                        <p class="text-[11px] font-semibold">{{ $discountLevel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'giftcard')
                <div class="flex items-start justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="opacity-50 uppercase">Saldo</p>
                        <p class="text-[22px] font-bold leading-none">${{ number_format($giftcardBalance, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="opacity-50 uppercase">Disponible</p>
                        <p class="text-[10px]">{{ $expiresLabel }}</p>
                    </div>
                </div>
            @elseif($typeSlug === 'points')
                <div class="flex items-start justify-between gap-3 text-[9px] mb-3">
                    <div>
                        <p class="opacity-50 uppercase">Puntos</p>
                        <p class="text-[22px] font-bold leading-none">{{ $pointsPreviewValue }}</p>
                    </div>
                    <div class="text-right">
                        @if($pointsPerPurchase)
                            <p class="text-[10px] opacity-60">{{ $pointsPerPurchase }} pts / compra</p>
                        @endif
                        <p class="text-[10px]">{{ $expiresLabel }}</p>
                    </div>
                </div>

                <div class="rounded-[12px] bg-white/65 border border-white/70 px-3 py-2 text-[10px]">
                    {{ $rewardText }}
                </div>
            @endif

            <div class="mt-3 rounded-[14px] bg-white/80 border border-white/70 p-3 min-h-[84px] flex flex-col items-center justify-center">
                @if($codeType === 'barcode')
                    @if($barcodeSvg)
                        <img src="{{ $barcodeSvg }}" class="mx-auto h-10 object-contain" alt="Barcode">
                    @else
                        <div class="h-10 w-32 rounded bg-white border border-gray-300"></div>
                    @endif
                    <p class="mt-2 text-[9px] tracking-[0.18em] uppercase text-gray-500 truncate max-w-full">
                        {{ $barcodeLabel }}
                    </p>
                @else
                    @if($qrSvg)
                        <img src="{{ $qrSvg }}" class="w-16 h-16 object-contain" alt="QR">
                    @else
                        <div class="w-16 h-16 bg-white border border-gray-300 rounded"></div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
