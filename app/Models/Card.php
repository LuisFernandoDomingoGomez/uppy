<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Card extends Model
{
    protected $fillable = [
        'card_type_id',
        'name',
        'slug',
        'status',
        'code_type',
        'main_image_path',
        'terms',
        'starts_at',
        'ends_at',
        'published_at',
        'is_unlimited',
        'is_active',
        'created_by',
        'updated_by',
        'settings_json',
        'meta_json',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
        'is_unlimited' => 'boolean',
        'is_active' => 'boolean',
        'settings_json' => 'array',
        'meta_json' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Card $card) {
            if (blank($card->slug)) {
                $base = Str::slug($card->name ?: 'tarjeta');
                $suffix = Str::lower(Str::random(6));
                $card->slug = "{$base}-{$suffix}";
            }
        });
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CardType::class, 'card_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function configs(): HasMany
    {
        return $this->hasMany(CardConfig::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(CardTier::class)->orderBy('sort_order');
    }

    public function design(): HasOne
    {
        return $this->hasOne(CardDesign::class);
    }

    public function notification(): HasOne
    {
        return $this->hasOne(CardNotification::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CardLink::class)->orderBy('sort_order');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CardSection::class)->orderBy('sort_order');
    }

    public function registrationFields(): HasMany
    {
        return $this->hasMany(CardRegistrationField::class)->orderBy('sort_order');
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $config = $this->configs->firstWhere('key', $key);

        if (! $config) {
            return $default;
        }

        return $config->value_json ?? $default;
    }

    public function setConfig(string $key, mixed $value): CardConfig
    {
        return $this->configs()->updateOrCreate(
            ['key' => $key],
            ['value_json' => $value]
        );
    }

    public function configsByPrefix(string $prefix): array
    {
        return $this->configs
            ->filter(fn ($config) => str_starts_with($config->key, $prefix))
            ->mapWithKeys(fn ($config) => [$config->key => $config->value_json])
            ->toArray();
    }
}
