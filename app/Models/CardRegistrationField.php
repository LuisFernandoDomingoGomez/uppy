<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardRegistrationField extends Model
{
    protected $fillable = [
        'card_id',
        'field_key',
        'label',
        'type',
        'is_required',
        'options_json',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options_json' => 'array',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
