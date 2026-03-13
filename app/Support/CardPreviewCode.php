<?php

namespace App\Support;

use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardPreviewCode
{
    public static function previewUrl(int $cardId): string
    {
        return url('/preview/card/' . $cardId);
    }

    public static function qrDataUri(int $cardId): string
    {
        $content = self::previewUrl($cardId);

        $svg = QrCode::format('svg')
            ->size(180)
            ->margin(1)
            ->generate($content);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function barcodeDataUri(int $cardId): string
    {
        $content = 'UPPY-' . str_pad((string) $cardId, 8, '0', STR_PAD_LEFT);

        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($content, $generator::TYPE_CODE_128, 2, 70);

        return 'data:image/png;base64,' . base64_encode($barcode);
    }

    public static function barcodeLabel(int $cardId): string
    {
        return 'UPPY-' . str_pad((string) $cardId, 8, '0', STR_PAD_LEFT);
    }
}
