<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardNotification extends Model
{
    protected $fillable = [
        'card_id',
        'birthday_enabled',
        'birthday_days_before',
        'birthday_message',
        'last_visit_enabled',
        'last_visit_days',
        'last_visit_message',
        'expiration_enabled',
        'expiration_message',
        'purchase_enabled',
        'purchase_message',
        'reward_enabled',
        'reward_message',
        'geo_enabled',
        'geo_radius_meters',
        'settings_json',
    ];

    protected $casts = [
        'birthday_enabled' => 'boolean',
        'last_visit_enabled' => 'boolean',
        'expiration_enabled' => 'boolean',
        'purchase_enabled' => 'boolean',
        'reward_enabled' => 'boolean',
        'geo_enabled' => 'boolean',
        'settings_json' => 'array',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
