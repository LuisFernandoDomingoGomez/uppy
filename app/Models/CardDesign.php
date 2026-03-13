<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardDesign extends Model
{
    protected $fillable = [
        'card_id',
        'background_color',
        'active_color',
        'inactive_color',
        'text_color',
        'logo_horizontal_path',
        'logo_square_path',
        'main_image_path',
        'stamp_active_image_path',
        'stamp_inactive_image_path',
        'stamp_active_icon_type',
        'stamp_active_icon_value',
        'stamp_inactive_icon_type',
        'stamp_inactive_icon_value',
        'preview_platform',
        'preview_json',
    ];

    protected $casts = [
        'preview_json' => 'array',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
