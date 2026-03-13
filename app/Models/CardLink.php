<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardLink extends Model
{
    protected $fillable = [
        'card_id',
        'type',
        'value',
        'label',
        'sort_order',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
