@extends('layouts.app')
@section('title', 'Overrides dépenses')
@section('page-title', 'Overrides des dépenses')

@section('content')
@php
$categoryLabels = [
    'personnel' => 'Personnel',
    'hebergement' => 'Hébergement',
    'infrastructure' => 'Infrastructure',
    'marketing' => 'Marketing',
    'locaux' => 'Locaux',
    'autre' => 'Autre',
];
@endphp

<div style="max-width:720px;">
    <div style="margin-bottom:24px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:20px;font-weight:700;letter-spacing:-0.03em;color:var(--text);margin-bottom:4px;">
                Ajustements — {{ $monthName }}
            </h1>
            <p style="font-size:13px;color:var(--text-3);">Modifiez ou désactivez des dépenses pour ce mois uniquement.</p>
        </div>
        <div style="display:flex;gap:6px;">
            <a href="{{ route('expenses.override', [$year, $month > 1 ? $month - 1 : 12]) }}" class="btn btn-secondary" style="padding:8px 12px;font-size:12px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Mois préc.
            </a>
            <a href="{{ route('expenses.override', [$year, $month < 12 ? $month + 1 : 1]) }}" class="btn btn-secondary" style="padding:8px 12px;font-size:12px;">
                Mois suiv.
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('expenses.storeOverride', [$year, $month]) }}">
        @csrf

        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:24px;">
            @forelse($expenses as $expense)
            @php
                $override = $overrides[$expense->id] ?? null;
                $isDefaultActive = $expense->isActiveForMonth($year, $month);
                $isEnabled = $override ? ($override->amount !== null) : $isDefaultActive;
                $currentAmount = $override ? ($override->amount ?? $expense->amount) : $expense->amount;
            @endphp
            <div class="card" style="border-radius:14px;" id="expense-card-{{ $expense->id }}">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0;">
                        {{-- Toggle switch --}}
                        <label class="toggle-wrap" for="enabled_{{ $expense->id }}" style="flex-shrink:0;">
                            <div class="toggle">
                                <input
                                    type="checkbox"
                                    name="overrides[{{ $expense->id }}][enabled]"
                                    value="1"
                                    {{ $isEnabled ? 'checked' : '' }}
                                    id="enabled_{{ $expense->id }}"
                                >
                                <span class="toggle-track"></span>
                                <span class="toggle-thumb"></span>
                            </div>
                        </label>
                        <div style="min-width:0;">
                            <label for="enabled_{{ $expense->id }}" style="font-size:13px;font-weight:600;color:var(--text);cursor:pointer;display:block;">{{ $expense->name }}</label>
                            <p style="font-size:11px;color:var(--text-3);margin-top:2px;">
                                {{ $categoryLabels[$expense->category] ?? $expense->category }}
                                <span style="color:var(--text-3);margin:0 4px;">·</span>
                                Base : <span style="color:var(--text-2);">{{ number_format($expense->amount, 2, ',', ' ') }} €</span>
                            </p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-end;gap:10px;flex-shrink:0;">
                        <div>
                            <label class="form-label" style="margin-bottom:4px;">Montant ce mois</label>
                            <div class="amount-input-wrap">
                                <input
                                    type="number"
                                    name="overrides[{{ $expense->id }}][amount]"
                                    value="{{ old('overrides.' . $expense->id . '.amount', $currentAmount) }}"
                                    step="0.01"
                                    min="0"
                                    class="amount-input"
                                    placeholder="{{ $expense->amount }}"
                                    style="width:140px;font-size:15px;"
                                >
                                <span class="amount-suffix">€</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <input
                        type="text"
                        name="overrides[{{ $expense->id }}][notes]"
                        value="{{ old('overrides.' . $expense->id . '.notes', $override?->notes ?? '') }}"
                        class="form-input"
                        placeholder="Notes pour ce mois (optionnel)"
                        style="font-size:12px;"
                    >
                </div>
            </div>
            @empty
            <div class="card" style="text-align:center;color:var(--text-3);">
                Aucune dépense récurrente — <a href="{{ route('expenses.create') }}" style="color:var(--accent);text-decoration:none;">Créer une dépense</a>
            </div>
            @endforelse
        </div>

        @if($expenses->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:10px;">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px 20px;font-size:14px;box-shadow:0 0 20px rgba(99,102,241,0.15);">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les ajustements
            </button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary" style="justify-content:center;">Annuler</a>
        </div>
        @endif
    </form>
</div>
@endsection
