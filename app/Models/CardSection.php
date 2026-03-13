<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardSection extends Model
{
    protected $fillable = [
        'card_id',
        'title',
        'content',
        'sort_order',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
