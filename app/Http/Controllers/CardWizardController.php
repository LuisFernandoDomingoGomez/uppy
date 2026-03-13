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
        $supportsStamps = $card->type->code === 'stamps';

        $validated = $request->validate([
            'code_type' => ['required', 'in:qr,barcode'],
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

            'main_image' => $supportsStamps
                ? ['nullable']
                : ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],

            'stamp_active_image' => $supportsStamps
                ? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096']
                : ['nullable'],

            'stamp_inactive_image' => $supportsStamps
                ? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096']
                : ['nullable'],
        ]);

        $design = $card->design()->updateOrCreate(
            ['card_id' => $card->id],
            [
                'background_color' => $validated['background_color'] ?? '#F3F4F6',
                'active_color' => $validated['active_color'] ?? '#2563EB',
                'inactive_color' => $validated['inactive_color'] ?? '#D1D5DB',
                'text_color' => $validated['text_color'] ?? '#111827',
                'stamp_active_icon_type' => $validated['stamp_active_icon_type'] ?? 'preset',
                'stamp_active_icon_value' => $validated['stamp_active_icon_value'] ?? 'star',
                'stamp_inactive_icon_type' => $validated['stamp_inactive_icon_type'] ?? 'preset',
                'stamp_inactive_icon_value' => $validated['stamp_inactive_icon_value'] ?? 'circle',
                'preview_json' => array_merge($card->design?->preview_json ?? [], [
                    'phone_frame' => true,
                    'code_type' => $validated['code_type'],
                ]),
            ]
        );

        if ($request->hasFile('logo_horizontal')) {
            $design->logo_horizontal_path = $request->file('logo_horizontal')->store('cards/logos-horizontal', 'public');
        }

        if ($request->hasFile('logo_square')) {
            $design->logo_square_path = $request->file('logo_square')->store('cards/logos-square', 'public');
        }

        if (! $supportsStamps && $request->hasFile('main_image')) {
            $design->main_image_path = $request->file('main_image')->store('cards/main-images', 'public');
        }

        if ($supportsStamps && $request->hasFile('stamp_active_image')) {
            $design->stamp_active_image_path = $request->file('stamp_active_image')->store('cards/stamps/active', 'public');
            $design->stamp_active_icon_type = 'upload';
        }

        if ($supportsStamps && $request->hasFile('stamp_inactive_image')) {
            $design->stamp_inactive_image_path = $request->file('stamp_inactive_image')->store('cards/stamps/inactive', 'public');
            $design->stamp_inactive_icon_type = 'upload';
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

    public function autosave(Request $request, Card $card)
    {
        $section = $request->input('section');

        return match ($section) {
            'step1' => $this->autosaveStep1($request, $card),
            'step2' => $this->autosaveStep2($request, $card),
            'step3' => $this->autosaveStep3($request, $card),
            'step4' => $this->autosaveStep4($request, $card),
            default => response()->json([
                'ok' => false,
                'message' => 'Sección inválida.',
            ], 422),
        };
    }

    private function autosaveStep1(Request $request, Card $card)
    {
        $typeCode = $card->type->code;

        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'expiration_mode' => ['nullable', 'in:unlimited,fixed_term,relative_after_issue'],
            'expires_at' => ['nullable', 'date'],
            'expires_after_value' => ['nullable', 'integer', 'min:1'],
            'expires_after_unit' => ['nullable', 'in:days,weeks,months,years'],
            'is_unlimited' => ['nullable', 'boolean'],
        ];

        if ($typeCode === 'stamps') {
            $rules = array_merge($rules, [
                'stamps_earning_mode' => ['nullable', 'in:visit,purchase,event'],
                'purchase_amount_required' => ['nullable', 'numeric', 'min:0'],
                'event_description' => ['nullable', 'string', 'max:255'],
                'stamps_required' => ['nullable', 'integer', 'min:1', 'max:21'],
                'stamps_on_signup' => ['nullable', 'integer', 'min:0'],
                'reward_text' => ['nullable', 'string', 'max:255'],
                'daily_limit_enabled' => ['nullable', 'boolean'],
            ]);
        }

        if ($typeCode === 'coupon') {
            $rules = array_merge($rules, [
                'coupon_valid_for' => ['nullable', 'string', 'max:255'],
                'coupon_replace_on_redeem' => ['nullable', 'boolean'],
            ]);
        }

        if ($typeCode === 'giftcard') {
            $rules = array_merge($rules, [
                'giftcard_initial_balance' => ['nullable', 'numeric', 'min:0'],
            ]);
        }

        if ($typeCode === 'points') {
            $rules = array_merge($rules, [
                'points_per_purchase' => ['nullable', 'numeric', 'min:0'],
                'reward_text' => ['nullable', 'string', 'max:255'],
            ]);
        }

        $validated = validator($request->all(), $rules)->validate();

        if (!empty($validated['name'])) {
            $card->name = $validated['name'];
        }

        $card->is_unlimited = (bool) ($validated['is_unlimited'] ?? $card->is_unlimited);
        $card->ends_at = ($validated['expiration_mode'] ?? null) === 'fixed_term'
            ? ($validated['expires_at'] ?? null)
            : null;
        $card->updated_by = auth()->id();
        $card->settings_json = array_merge($card->settings_json ?? [], [
            'wizard_step' => max(($card->settings_json['wizard_step'] ?? 1), 1),
        ]);
        $card->save();

        if (array_key_exists('expiration_mode', $validated)) {
            $card->setConfig('general.expiration_mode', $validated['expiration_mode']);
        }

        if (array_key_exists('is_unlimited', $validated)) {
            $card->setConfig('general.is_unlimited', (bool) $validated['is_unlimited']);
        }

        if (array_key_exists('expires_at', $validated)) {
            $card->setConfig('general.expires_at', $validated['expires_at']);
        }

        if (array_key_exists('expires_after_value', $validated)) {
            $card->setConfig('general.expires_after_value', $validated['expires_after_value']);
        }

        if (array_key_exists('expires_after_unit', $validated)) {
            $card->setConfig('general.expires_after_unit', $validated['expires_after_unit']);
        }

        if ($typeCode === 'stamps') {
            if (array_key_exists('stamps_earning_mode', $validated)) {
                $card->setConfig('stamps.earning_mode', $validated['stamps_earning_mode']);
            }
            if (array_key_exists('purchase_amount_required', $validated)) {
                $card->setConfig('stamps.purchase_amount_required', $validated['purchase_amount_required']);
            }
            if (array_key_exists('event_description', $validated)) {
                $card->setConfig('stamps.event_description', $validated['event_description']);
            }
            if (array_key_exists('stamps_required', $validated)) {
                $card->setConfig('stamps.required', $validated['stamps_required']);
            }
            if (array_key_exists('stamps_on_signup', $validated)) {
                $card->setConfig('stamps.on_signup', $validated['stamps_on_signup']);
            }
            if (array_key_exists('reward_text', $validated)) {
                $card->setConfig('stamps.reward_text', $validated['reward_text']);
            }
            if (array_key_exists('daily_limit_enabled', $validated)) {
                $card->setConfig('stamps.daily_limit_enabled', (bool) $validated['daily_limit_enabled']);
            }
        }

        if ($typeCode === 'coupon') {
            if (array_key_exists('coupon_valid_for', $validated)) {
                $card->setConfig('coupon.valid_for', $validated['coupon_valid_for']);
            }
            if (array_key_exists('coupon_replace_on_redeem', $validated)) {
                $card->setConfig('coupon.replace_on_redeem', (bool) $validated['coupon_replace_on_redeem']);
            }
        }

        if ($typeCode === 'giftcard' && array_key_exists('giftcard_initial_balance', $validated)) {
            $card->setConfig('giftcard.initial_balance', (float) $validated['giftcard_initial_balance']);
        }

        if ($typeCode === 'points') {
            if (array_key_exists('points_per_purchase', $validated)) {
                $card->setConfig('points.per_purchase', (float) $validated['points_per_purchase']);
            }
            if (array_key_exists('reward_text', $validated)) {
                $card->setConfig('points.reward_text', $validated['reward_text']);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Paso 1 guardado',
        ]);
    }

    private function autosaveStep2(Request $request, Card $card)
    {
        $validated = validator($request->all(), [
            'code_type' => ['nullable', 'in:qr,barcode'],
            'background_color' => ['nullable', 'string', 'max:20'],
            'active_color' => ['nullable', 'string', 'max:20'],
            'inactive_color' => ['nullable', 'string', 'max:20'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'stamp_active_icon_type' => ['nullable', 'in:preset,upload,emoji,svg'],
            'stamp_active_icon_value' => ['nullable', 'string', 'max:255'],
            'stamp_inactive_icon_type' => ['nullable', 'in:preset,upload,emoji,svg'],
            'stamp_inactive_icon_value' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $design = $card->design()->updateOrCreate(
            ['card_id' => $card->id],
            [
                'background_color' => $validated['background_color'] ?? ($card->design?->background_color ?? '#F3F4F6'),
                'active_color' => $validated['active_color'] ?? ($card->design?->active_color ?? '#2563EB'),
                'inactive_color' => $validated['inactive_color'] ?? ($card->design?->inactive_color ?? '#D1D5DB'),
                'text_color' => $validated['text_color'] ?? ($card->design?->text_color ?? '#111827'),
                'stamp_active_icon_type' => $validated['stamp_active_icon_type'] ?? ($card->design?->stamp_active_icon_type ?? 'preset'),
                'stamp_active_icon_value' => $validated['stamp_active_icon_value'] ?? ($card->design?->stamp_active_icon_value ?? 'star'),
                'stamp_inactive_icon_type' => $validated['stamp_inactive_icon_type'] ?? ($card->design?->stamp_inactive_icon_type ?? 'preset'),
                'stamp_inactive_icon_value' => $validated['stamp_inactive_icon_value'] ?? ($card->design?->stamp_inactive_icon_value ?? 'circle'),
                'preview_json' => array_merge($card->design?->preview_json ?? [], [
                    'phone_frame' => true,
                    'code_type' => $validated['code_type'] ?? ($card->code_type ?? 'qr'),
                ]),
            ]
        );

        if (array_key_exists('code_type', $validated)) {
            $card->code_type = $validated['code_type'];
        }

        $card->updated_by = auth()->id();
        $card->settings_json = array_merge($card->settings_json ?? [], [
            'wizard_step' => max(($card->settings_json['wizard_step'] ?? 1), 2),
        ]);
        $card->save();

        return response()->json([
            'ok' => true,
            'message' => 'Paso 2 guardado',
            'files' => [
                'logo_horizontal_path' => $design->logo_horizontal_path,
                'logo_square_path' => $design->logo_square_path,
                'main_image_path' => $design->main_image_path,
            ],
        ]);
    }

    private function autosaveStep3(Request $request, Card $card)
    {
        $validated = validator($request->all(), [
            'display_name' => ['nullable', 'string', 'max:255'],
            'terms' => ['nullable', 'string'],
            'links' => ['nullable', 'array'],
            'links.*.type' => ['nullable', 'in:url,phone,whatsapp,instagram,facebook,tiktok,email'],
            'links.*.value' => ['nullable', 'string', 'max:255'],
            'links.*.label' => ['nullable', 'string', 'max:255'],
            'sections' => ['nullable', 'array'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.content' => ['nullable', 'string', 'max:2000'],
        ])->validate();

        $card->terms = $validated['terms'] ?? $card->terms;
        $card->updated_by = auth()->id();
        $card->settings_json = array_merge($card->settings_json ?? [], [
            'wizard_step' => max(($card->settings_json['wizard_step'] ?? 1), 3),
            'display_name' => $validated['display_name'] ?? ($card->settings_json['display_name'] ?? $card->name),
        ]);
        $card->save();

        if (array_key_exists('links', $validated)) {
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
        }

        if (array_key_exists('sections', $validated)) {
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
        }

        return response()->json([
            'ok' => true,
            'message' => 'Paso 3 guardado',
        ]);
    }

    private function autosaveStep4(Request $request, Card $card)
    {
        $validated = validator($request->all(), [
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
        ])->validate();

        $currentSettings = $card->notification?->settings_json ?? [];

        $card->notification()->updateOrCreate(
            ['card_id' => $card->id],
            [
                'birthday_enabled' => (bool) ($validated['birthday_enabled'] ?? false),
                'birthday_days_before' => $validated['birthday_days_before'] ?? 7,
                'birthday_message' => $validated['birthday_message'] ?? null,

                'last_visit_enabled' => (bool) ($validated['last_visit_enabled'] ?? false),
                'last_visit_days' => $validated['last_visit_days_after'] ?? 30,
                'last_visit_message' => $validated['last_visit_message'] ?? null,

                'expiration_enabled' => (bool) ($validated['expiration_enabled'] ?? false),
                'expiration_message' => $validated['expiration_message'] ?? null,

                'purchase_enabled' => (bool) ($validated['purchase_enabled'] ?? false),
                'purchase_message' => $validated['purchase_message'] ?? null,

                'reward_enabled' => (bool) ($validated['reward_enabled'] ?? false),
                'reward_message' => $validated['reward_message'] ?? null,

                'geo_enabled' => (bool) ($validated['geo_enabled'] ?? false),
                'geo_radius_meters' => $validated['geo_radius_meters'] ?? 100,

                'settings_json' => array_merge($currentSettings, [
                    'geo_message' => $validated['geo_message'] ?? null,
                ]),
            ]
        );

        $card->updated_by = auth()->id();
        $card->settings_json = array_merge($card->settings_json ?? [], [
            'wizard_step' => max(($card->settings_json['wizard_step'] ?? 1), 4),
        ]);
        $card->save();

        return response()->json([
            'ok' => true,
            'message' => 'Paso 4 guardado',
        ]);
    }
}
