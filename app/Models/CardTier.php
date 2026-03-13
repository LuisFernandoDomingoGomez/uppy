<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardTier extends Model
{
    protected $fillable = [
        'card_id',
        'tier_type',
        'name',
        'threshold_amount',
        'percentage',
        'reward_value',
        'sort_order',
        'meta_json',
    ];

    protected $casts = [
        'threshold_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'reward_value' => 'decimal:2',
        'meta_json' => 'array',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
