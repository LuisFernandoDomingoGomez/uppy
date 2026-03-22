@php
    $design = $card->design;
    $settings = is_array($card->settings_json) ? $card->settings_json : [];

    $backgroundColor = $design?->background_color ?: '#F3F4F6';
    $activeColor = $design?->active_color ?: '#2563EB';
    $inactiveColor = $design?->inactive_color ?: '#D1D5DB';
    $textColor = $design?->text_color ?: '#111827';

    $previewPlatform = strtolower($design?->preview_platform ?? 'ios');
    $codeType = strtolower($card->code_type ?? 'qr');

    $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;
    $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
    $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
    $stampActiveImageUrl = $design?->stamp_active_image_path ? asset('storage/' . $design->stamp_active_image_path) : null;
    $stampInactiveImageUrl = $design?->stamp_inactive_image_path ? asset('storage/' . $design->stamp_inactive_image_path) : null;

    $expiresLabel = 'Sin vencimiento';
    if (!empty($card->ends_at)) {
        $expiresLabel = 'Vence ' . $card->ends_at->format('d/m/Y');
    } elseif (!empty($settings['expiration_mode']) && $settings['expiration_mode'] !== 'unlimited') {
        if (($settings['expiration_mode'] ?? null) === 'fixed_term' && !empty($settings['expires_at'])) {
            try {
                $expiresLabel = 'Vence ' . \Carbon\Carbon::parse($settings['expires_at'])->format('d/m/Y');
            } catch (\Throwable $e) {
                $expiresLabel = 'Con vencimiento';
            }
        } elseif (($settings['expiration_mode'] ?? null) === 'relative_after_issue' && !empty($settings['expires_after_value']) && !empty($settings['expires_after_unit'])) {
            $expiresLabel = 'Vence en ' . $settings['expires_after_value'] . ' ' . $settings['expires_after_unit'];
        } else {
            $expiresLabel = 'Con vencimiento';
        }
    }

    $couponValidFor = $settings['coupon_valid_for'] ?? 'Un beneficio';
    $rewardText = $settings['reward_text'] ?? 'Recompensa disponible';
    $giftcardBalance = (float) ($settings['giftcard_initial_balance'] ?? 0);
    $pointsPerPurchase = $settings['points_per_purchase'] ?? null;
    $pointsPreviewValue = $settings['points_preview_balance'] ?? $settings['initial_points'] ?? 120;

    $stampsOnSignup = (int) ($settings['stamps_on_signup'] ?? 1);
    $stampSlots = 10;
    $filledStampCount = max(1, min($stampSlots, $stampsOnSignup));

    $tiers = $card->relationLoaded('tiers') ? $card->tiers : $card->tiers()->get();
    $firstTier = $tiers->sortBy('position')->first();

    $cashbackPercent = data_get($firstTier, 'percentage')
        ?? data_get($firstTier, 'cashback_percentage')
        ?? data_get($firstTier, 'value')
        ?? '0';

    $cashbackLevel = data_get($firstTier, 'name')
        ?? data_get($firstTier, 'level_name')
        ?? 'Bronce';

    $discountPercent = data_get($firstTier, 'percentage')
        ?? data_get($firstTier, 'discount_percentage')
        ?? data_get($firstTier, 'value')
        ?? '0';

    $discountLevel = data_get($firstTier, 'name')
        ?? data_get($firstTier, 'level_name')
        ?? 'Nivel 1';

    $qrSvg = null;
    $barcodeSvg = null;
    $barcodeLabel = $card->slug ?: ('CARD-' . $card->id);

    if (class_exists(\App\Support\CardPreviewCode::class)) {
        try {
            $qrSvg = \App\Support\CardPreviewCode::qrDataUri($card->id);
        } catch (\Throwable $e) {
            $qrSvg = null;
        }

        try {
            $barcodeSvg = \App\Support\CardPreviewCode::barcodeDataUri($card->id);
        } catch (\Throwable $e) {
            $barcodeSvg = null;
        }

        try {
            $barcodeLabel = \App\Support\CardPreviewCode::barcodeLabel($card->id);
        } catch (\Throwable $e) {
            $barcodeLabel = $card->slug ?: ('CARD-' . $card->id);
        }
    }

    $supportsStamps = $typeSlug === 'stamps';
    $isIos = $previewPlatform !== 'android';
@endphp

<div class="rounded-[24px] border border-gray-200 bg-gray-50 p-3 shadow-inner">
    @if($isIos)
        @include('cards.partials.preview-ios', [
            'card' => $card,
            'typeSlug' => $typeSlug,
            'typeName' => $typeName,
            'displayName' => $displayName,
            'backgroundColor' => $backgroundColor,
            'activeColor' => $activeColor,
            'inactiveColor' => $inactiveColor,
            'textColor' => $textColor,
            'codeType' => $codeType,
            'mainImageUrl' => $mainImageUrl,
            'logoHorizontalUrl' => $logoHorizontalUrl,
            'logoSquareUrl' => $logoSquareUrl,
            'stampActiveImageUrl' => $stampActiveImageUrl,
            'stampInactiveImageUrl' => $stampInactiveImageUrl,
            'expiresLabel' => $expiresLabel,
            'couponValidFor' => $couponValidFor,
            'rewardText' => $rewardText,
            'giftcardBalance' => $giftcardBalance,
            'pointsPerPurchase' => $pointsPerPurchase,
            'pointsPreviewValue' => $pointsPreviewValue,
            'stampSlots' => $stampSlots,
            'filledStampCount' => $filledStampCount,
            'cashbackPercent' => $cashbackPercent,
            'cashbackLevel' => $cashbackLevel,
            'discountPercent' => $discountPercent,
            'discountLevel' => $discountLevel,
            'qrSvg' => $qrSvg,
            'barcodeSvg' => $barcodeSvg,
            'barcodeLabel' => $barcodeLabel,
            'supportsStamps' => $supportsStamps,
        ])
    @else
        @include('cards.partials.preview-android', [
            'card' => $card,
            'typeSlug' => $typeSlug,
            'typeName' => $typeName,
            'displayName' => $displayName,
            'backgroundColor' => $backgroundColor,
            'activeColor' => $activeColor,
            'inactiveColor' => $inactiveColor,
            'textColor' => $textColor,
            'codeType' => $codeType,
            'mainImageUrl' => $mainImageUrl,
            'logoHorizontalUrl' => $logoHorizontalUrl,
            'logoSquareUrl' => $logoSquareUrl,
            'stampActiveImageUrl' => $stampActiveImageUrl,
            'stampInactiveImageUrl' => $stampInactiveImageUrl,
            'expiresLabel' => $expiresLabel,
            'couponValidFor' => $couponValidFor,
            'rewardText' => $rewardText,
            'giftcardBalance' => $giftcardBalance,
            'pointsPerPurchase' => $pointsPerPurchase,
            'pointsPreviewValue' => $pointsPreviewValue,
            'stampSlots' => $stampSlots,
            'filledStampCount' => $filledStampCount,
            'cashbackPercent' => $cashbackPercent,
            'cashbackLevel' => $cashbackLevel,
            'discountPercent' => $discountPercent,
            'discountLevel' => $discountLevel,
            'qrSvg' => $qrSvg,
            'barcodeSvg' => $barcodeSvg,
            'barcodeLabel' => $barcodeLabel,
            'supportsStamps' => $supportsStamps,
        ])
    @endif
</div>
