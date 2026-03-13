<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardDesign extends Model
{
    protected $fillable = [
        'card_id',
        'main_image_path',
        'logo_horizontal_path',
        'logo_square_path',
        'preview_platform',
        'stamp_active_icon_type',
        'stamp_active_icon_value',
        'stamp_inactive_icon_type',
        'stamp_inactive_icon_value',
        'background_color',
        'active_color',
        'inactive_color',
        'text_color',
        'logo_path',
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
