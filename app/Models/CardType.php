<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CardType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'supports_rewards',
        'supports_balance',
        'supports_stamps',
        'supports_notifications',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'supports_rewards' => 'boolean',
        'supports_balance' => 'boolean',
        'supports_stamps' => 'boolean',
        'supports_notifications' => 'boolean',
    ];

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
