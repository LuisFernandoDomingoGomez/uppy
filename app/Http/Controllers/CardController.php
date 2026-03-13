<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::with(['type', 'design'])
            ->latest()
            ->get();

        return view('cards.index', compact('cards'));
    }

    public function create()
    {
        $cardTypes = CardType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('cards.create', compact('cardTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_type_id' => ['required', 'exists:card_types,id'],
        ]);

        $type = CardType::findOrFail($validated['card_type_id']);

        $card = Card::create([
            'card_type_id' => $type->id,
            'name' => $type->name . ' ' . now()->format('YmdHis'),
            'slug' => Str::slug($type->name . ' ' . now()->format('YmdHis')) . '-' . Str::lower(Str::random(5)),
            'status' => 'draft',
            'is_active' => false,
            'code_type' => 'qr',
            'is_unlimited' => true,
            'settings_json' => [
                'wizard_step' => 1,
                'display_name' => $type->name,
            ],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('cards.wizard.step1', $card)
            ->with('success', 'Borrador de tarjeta creado correctamente.');
    }

    public function show(Card $card)
    {
        $card->load([
            'type',
            'design',
            'configs',
            'tiers',
            'links',
            'sections',
            'notification',
        ]);

        return view('cards.show', compact('card'));
    }

    public function edit(Card $card)
    {
        return redirect()->route('cards.wizard.step1', $card);
    }
}
