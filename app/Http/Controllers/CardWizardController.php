<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardWizardController extends Controller
{
    public function step1(Card $card)
    {
        $card->load(['type', 'configs', 'tiers', 'registrationFields', 'design', 'links', 'sections', 'notification']);

        return view('cards.wizard.step1', compact('card'));
    }

    public function saveStep1(Request $request, Card $card)
    {
        $typeCode = $card->type->code;

        $commonRules = [
            'name' => ['required', 'string', 'max:255'],
            'expiration_mode' => ['required', 'in:unlimited,fixed_term,relative_after_issue'],
            'expires_at' => ['nullable', 'date'],
            'expires_after_value' => ['nullable', 'integer', 'min:1'],
            'expires_after_unit' => ['nullable', 'in:days,weeks,months,years'],
            'is_unlimited' => ['nullable', 'boolean'],
        ];

        $specificRules = match ($typeCode) {
            'stamps' => [
                'stamps_earning_mode' => ['required', 'in:visit,purchase,event'],
                'purchase_amount_required' => ['nullable', 'numeric', 'min:0'],
                'event_description' => ['nullable', 'string', 'max:255'],
                'stamps_required' => ['required', 'integer', 'min:1', 'max:21'],
                'stamps_on_signup' => ['nullable', 'integer', 'min:0'],
                'reward_text' => ['required', 'string', 'max:255'],
                'daily_limit_enabled' => ['nullable', 'boolean'],
            ],
            'coupon' => [
                'coupon_valid_for' => ['required', 'string', 'max:255'],
                'coupon_replace_on_redeem' => ['nullable', 'boolean'],
            ],
            'cashback' => [
                'tiers' => ['required', 'array', 'min:1'],
                'tiers.*.name' => ['nullable', 'string', 'max:255'],
                'tiers.*.threshold_amount' => ['required', 'numeric', 'min:0'],
                'tiers.*.percentage' => ['required', 'numeric', 'min:0'],
            ],
            'discount_levels' => [
                'tiers' => ['required', 'array', 'min:1'],
                'tiers.*.name' => ['nullable', 'string', 'max:255'],
                'tiers.*.threshold_amount' => ['required', 'numeric', 'min:0'],
                'tiers.*.percentage' => ['required', 'numeric', 'min:0'],
            ],
            'giftcard' => [
                'giftcard_initial_balance' => ['nullable', 'numeric', 'min:0'],
            ],
            'points' => [
                'points_per_purchase' => ['nullable', 'numeric', 'min:0'],
                'reward_text' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };

        $validated = $request->validate(array_merge($commonRules, $specificRules));

        $card->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::lower(Str::random(6)),
            'is_unlimited' => (bool) ($validated['is_unlimited'] ?? false),
            'ends_at' => $validated['expiration_mode'] === 'fixed_term'
                ? ($validated['expires_at'] ?? null)
                : null,
            'updated_by' => auth()->id(),
            'settings_json' => array_merge($card->settings_json ?? [], [
                'wizard_step' => 2,
            ]),
        ]);

        $card->setConfig('general.expiration_mode', $validated['expiration_mode']);
        $card->setConfig('general.is_unlimited', (bool) ($validated['is_unlimited'] ?? false));
        $card->setConfig('general.expires_at', $validated['expires_at'] ?? null);
        $card->setConfig('general.expires_after_value', $validated['expires_after_value'] ?? null);
        $card->setConfig('general.expires_after_unit', $validated['expires_after_unit'] ?? null);

        if (in_array($typeCode, ['cashback', 'discount_levels'])) {
            $card->tiers()->delete();
        }

        match ($typeCode) {
            'stamps' => $this->saveStampsConfig($card, $validated),
            'coupon' => $this->saveCouponConfig($card, $validated),
            'cashback' => $this->saveCashbackTiers($card, $validated['tiers']),
            'discount_levels' => $this->saveDiscountTiers($card, $validated['tiers']),
            'giftcard' => $this->saveGiftcardConfig($card, $validated),
            'points' => $this->savePointsConfig($card, $validated),
            default => null,
        };

        return redirect()
            ->route('cards.wizard.step1', $card)
            ->with('success', 'Paso 1 guardado correctamente.');
    }

    public function step2(Card $card)
    {
        $card->load(['type', 'design', 'configs', 'tiers']);

        return view('cards.wizard.step2', compact('card'));
    }

    public function saveStep2(Request $request, Card $card)
    {
        $validated = $request->validate([
            'code_type' => ['required', 'in:qr,barcode'],
            'preview_platform' => ['required', 'in:ios,android'],
            'background_color' => ['nullable', 'string', 'max:20'],
            'active_color' => ['nullable', 'string', 'max:20'],
            'inactive_color' => ['nullable', 'string', 'max:20'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'stamp_active_icon_type' => ['nullable', 'in:preset,upload,emoji,svg'],
            'stamp_active_icon_value' => ['nullable', 'string', 'max:255'],
            'stamp_inactive_icon_type' => ['nullable', 'in:preset,upload,emoji,svg'],
            'stamp_inactive_icon_value' => ['nullable', 'string', 'max:255'],
            'logo_horizontal' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'logo_square' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'main_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ]);

        $design = $card->design()->updateOrCreate(
            ['card_id' => $card->id],
            [
                'preview_platform' => $validated['preview_platform'],
                'background_color' => $validated['background_color'] ?? '#F3F4F6',
                'active_color' => $validated['active_color'] ?? '#2563EB',
                'inactive_color' => $validated['inactive_color'] ?? '#D1D5DB',
                'text_color' => $validated['text_color'] ?? '#111827',
                'stamp_active_icon_type' => $validated['stamp_active_icon_type'] ?? 'preset',
                'stamp_active_icon_value' => $validated['stamp_active_icon_value'] ?? 'star',
                'stamp_inactive_icon_type' => $validated['stamp_inactive_icon_type'] ?? 'preset',
                'stamp_inactive_icon_value' => $validated['stamp_inactive_icon_value'] ?? 'circle',
                'preview_json' => [
                    'phone_frame' => true,
                    'code_type' => $validated['code_type'],
                    'preview_platform' => $validated['preview_platform'],
                ],
            ]
        );

        if ($request->hasFile('logo_horizontal')) {
            $design->logo_horizontal_path = $request->file('logo_horizontal')->store('cards/logos-horizontal', 'public');
        }

        if ($request->hasFile('logo_square')) {
            $design->logo_square_path = $request->file('logo_square')->store('cards/logos-square', 'public');
        }

        if ($request->hasFile('main_image')) {
            $design->main_image_path = $request->file('main_image')->store('cards/main-images', 'public');
        }

        $design->save();

        $card->update([
            'code_type' => $validated['code_type'],
            'updated_by' => auth()->id(),
            'settings_json' => array_merge($card->settings_json ?? [], [
                'wizard_step' => 3,
            ]),
        ]);

        return redirect()
            ->route('cards.wizard.step2', $card)
            ->with('success', 'Paso 2 guardado correctamente.');
    }

    public function step3(Card $card)
    {
        $card->load(['type', 'design', 'configs', 'tiers', 'links', 'sections']);

        return view('cards.wizard.step3', compact('card'));
    }

    public function saveStep3(Request $request, Card $card)
    {
        $validated = $request->validate([
            'display_name' => ['nullable', 'string', 'max:255'],
            'terms' => ['nullable', 'string'],
            'links' => ['nullable', 'array'],
            'links.*.type' => ['nullable', 'in:url,phone,whatsapp,instagram,facebook,tiktok,email'],
            'links.*.value' => ['nullable', 'string', 'max:255'],
            'links.*.label' => ['nullable', 'string', 'max:255'],
            'sections' => ['nullable', 'array'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.content' => ['nullable', 'string', 'max:2000'],
        ]);

        $card->update([
            'terms' => $validated['terms'] ?? null,
            'updated_by' => auth()->id(),
            'settings_json' => array_merge($card->settings_json ?? [], [
                'wizard_step' => 4,
                'display_name' => $validated['display_name'] ?? $card->name,
            ]),
        ]);

        $card->links()->delete();
        foreach (($validated['links'] ?? []) as $index => $link) {
            if (blank($link['type'] ?? null) && blank($link['value'] ?? null) && blank($link['label'] ?? null)) {
                continue;
            }

            $card->links()->create([
                'type' => $link['type'] ?: 'url',
                'value' => $link['value'] ?? null,
                'label' => $link['label'] ?? null,
                'sort_order' => $index + 1,
            ]);
        }

        $card->sections()->delete();
        foreach (($validated['sections'] ?? []) as $index => $section) {
            if (blank($section['title'] ?? null) && blank($section['content'] ?? null)) {
                continue;
            }

            $card->sections()->create([
                'title' => $section['title'] ?? null,
                'content' => $section['content'] ?? null,
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()
            ->route('cards.wizard.step3', $card)
            ->with('success', 'Paso 3 guardado correctamente.');
    }

    public function step4(Card $card)
    {
        $card->load(['type', 'design', 'notification']);

        return view('cards.wizard.step4', compact('card'));
    }

    public function saveStep4(Request $request, Card $card)
    {
        $validated = $request->validate([
            'birthday_enabled' => ['nullable', 'boolean'],
            'birthday_days_before' => ['nullable', 'integer', 'min:0', 'max:365'],
            'birthday_message' => ['nullable', 'string', 'max:500'],

            'last_visit_enabled' => ['nullable', 'boolean'],
            'last_visit_days_after' => ['nullable', 'integer', 'min:1', 'max:365'],
            'last_visit_message' => ['nullable', 'string', 'max:500'],

            'expiration_enabled' => ['nullable', 'boolean'],
            'expiration_message' => ['nullable', 'string', 'max:500'],

            'purchase_enabled' => ['nullable', 'boolean'],
            'purchase_message' => ['nullable', 'string', 'max:500'],

            'reward_enabled' => ['nullable', 'boolean'],
            'reward_message' => ['nullable', 'string', 'max:500'],

            'geo_enabled' => ['nullable', 'boolean'],
            'geo_radius_meters' => ['nullable', 'integer', 'min:1', 'max:50000'],
            'geo_message' => ['nullable', 'string', 'max:500'],
        ]);

        $currentSettings = $card->notification?->settings_json ?? [];

        $card->notification()->updateOrCreate(
            ['card_id' => $card->id],
            [
                'birthday_enabled' => (bool) $request->boolean('birthday_enabled'),
                'birthday_days_before' => $validated['birthday_days_before'] ?? 7,
                'birthday_message' => $validated['birthday_message'] ?? '¡Feliz cumpleaños! 🎉',

                'last_visit_enabled' => (bool) $request->boolean('last_visit_enabled'),
                'last_visit_days' => $validated['last_visit_days_after'] ?? 30,
                'last_visit_message' => $validated['last_visit_message'] ?? 'Te extrañamos. Vuelve pronto.',

                'expiration_enabled' => (bool) $request->boolean('expiration_enabled'),
                'expiration_message' => $validated['expiration_message'] ?? 'Tu tarjeta está por vencer.',

                'purchase_enabled' => (bool) $request->boolean('purchase_enabled'),
                'purchase_message' => $validated['purchase_message'] ?? 'Gracias por tu compra.',

                'reward_enabled' => (bool) $request->boolean('reward_enabled'),
                'reward_message' => $validated['reward_message'] ?? '¡Ya tienes una recompensa disponible!',

                'geo_enabled' => (bool) $request->boolean('geo_enabled'),
                'geo_radius_meters' => $validated['geo_radius_meters'] ?? 100,

                'settings_json' => array_merge($currentSettings, [
                    'geo_message' => $validated['geo_message'] ?? 'Estás cerca de una sucursal.',
                ]),
            ]
        );

        $card->update([
            'updated_by' => auth()->id(),
            'settings_json' => array_merge($card->settings_json ?? [], [
                'wizard_step' => 5,
            ]),
        ]);

        return redirect()
            ->route('cards.wizard.step4', $card)
            ->with('success', 'Paso 4 guardado correctamente.');
    }

    public function publish(Card $card)
    {
        $card->update([
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('cards.show', $card)
            ->with('success', 'Tarjeta activada correctamente.');
    }

    public function pause(Card $card)
    {
        $card->update([
            'status' => 'inactive',
            'is_active' => false,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('cards.show', $card)
            ->with('success', 'Tarjeta desactivada correctamente.');
    }

    private function saveStampsConfig(Card $card, array $validated): void
    {
        $card->setConfig('stamps.earning_mode', $validated['stamps_earning_mode']);
        $card->setConfig('stamps.purchase_amount_required', $validated['purchase_amount_required'] ?? null);
        $card->setConfig('stamps.event_description', $validated['event_description'] ?? null);
        $card->setConfig('stamps.required', (int) $validated['stamps_required']);
        $card->setConfig('stamps.on_signup', (int) ($validated['stamps_on_signup'] ?? 0));
        $card->setConfig('stamps.reward_text', $validated['reward_text']);
        $card->setConfig('stamps.daily_limit_enabled', (bool) ($validated['daily_limit_enabled'] ?? false));
    }

    private function saveCouponConfig(Card $card, array $validated): void
    {
        $card->setConfig('coupon.valid_for', $validated['coupon_valid_for']);
        $card->setConfig('coupon.replace_on_redeem', (bool) ($validated['coupon_replace_on_redeem'] ?? false));
    }

    private function saveCashbackTiers(Card $card, array $tiers): void
    {
        foreach ($tiers as $index => $tier) {
            if (
                blank($tier['name'] ?? null) &&
                blank($tier['threshold_amount'] ?? null) &&
                blank($tier['percentage'] ?? null)
            ) {
                continue;
            }

            $card->tiers()->create([
                'tier_type' => 'cashback_level',
                'name' => $tier['name'] ?? null,
                'threshold_amount' => $tier['threshold_amount'],
                'percentage' => $tier['percentage'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function saveDiscountTiers(Card $card, array $tiers): void
    {
        foreach ($tiers as $index => $tier) {
            if (
                blank($tier['name'] ?? null) &&
                blank($tier['threshold_amount'] ?? null) &&
                blank($tier['percentage'] ?? null)
            ) {
                continue;
            }

            $card->tiers()->create([
                'tier_type' => 'discount_level',
                'name' => $tier['name'] ?? null,
                'threshold_amount' => $tier['threshold_amount'],
                'percentage' => $tier['percentage'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function saveGiftcardConfig(Card $card, array $validated): void
    {
        $card->setConfig('giftcard.initial_balance', (float) ($validated['giftcard_initial_balance'] ?? 0));
    }

    private function savePointsConfig(Card $card, array $validated): void
    {
        $card->setConfig('points.per_purchase', (float) ($validated['points_per_purchase'] ?? 0));
        $card->setConfig('points.reward_text', $validated['reward_text'] ?? null);
    }
}
