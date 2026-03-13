<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardConfig extends Model
{
    protected $fillable = [
        'card_id',
        'key',
        'value_json',
    ];

    protected $casts = [
        'value_json' => 'array',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
