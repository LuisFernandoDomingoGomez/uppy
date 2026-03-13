<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardDesign;
use App\Models\CardNotification;
use App\Models\CardType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::with(['type'])
            ->latest()
            ->paginate(12);

        return view('cards.index', compact('cards'));
    }

    public function create()
    {
        $cardTypes = CardType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('cards.create', compact('cardTypes'));
    }

    public function storeDraft(Request $request)
    {
        $data = $request->validate([
            'card_type_id' => ['required', 'exists:card_types,id'],
        ], [
            'card_type_id.required' => 'Debes seleccionar un tipo de tarjeta.',
        ]);

        $type = CardType::findOrFail($data['card_type_id']);

        $defaultName = $type->name . ' ' . now()->format('YmdHis');

        $card = Card::create([
            'card_type_id' => $type->id,
            'name' => $defaultName,
            'slug' => Str::slug($defaultName) . '-' . Str::lower(Str::random(6)),
            'status' => 'draft',
            'code_type' => 'qr',
            'is_unlimited' => true,
            'is_active' => false,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'settings_json' => [
                'wizard_step' => 1,
            ],
            'meta_json' => [
                'type_code' => $type->code,
            ],
        ]);

        CardDesign::create([
            'card_id' => $card->id,
            'background_color' => '#F3F4F6',
            'active_color' => '#2563EB',
            'inactive_color' => '#D1D5DB',
            'text_color' => '#111827',
            'preview_json' => [
                'phone_frame' => true,
            ],
        ]);

        CardNotification::create([
            'card_id' => $card->id,
        ]);

        return redirect()
            ->route('cards.edit', $card)
            ->with('success', 'Borrador de tarjeta creado correctamente.');
    }

    public function edit(Card $card)
    {
        $card->load(['type', 'design', 'notification', 'configs', 'tiers']);

        return view('cards.edit', compact('card'));
    }
}
