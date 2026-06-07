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

<div class="mb-6">
    <h2 class="text-xl font-bold text-white">Dépenses de {{ $monthName }}</h2>
    <p class="text-zinc-400 text-sm mt-1">Activez, désactivez ou modifiez les montants pour ce mois uniquement.</p>
</div>

<form method="POST" action="{{ route('expenses.storeOverride', [$year, $month]) }}">
    @csrf

    <div class="space-y-3 mb-6">
        @forelse($expenses as $expense)
        @php
            $override = $overrides[$expense->id] ?? null;
            $isDefaultActive = $expense->isActiveForMonth($year, $month);
            $isEnabled = $override ? ($override->amount !== null) : $isDefaultActive;
            $currentAmount = $override ? ($override->amount ?? $expense->amount) : $expense->amount;
        @endphp
        <div class="card">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 flex-1">
                    <label class="flex items-center gap-2 cursor-pointer mt-0.5">
                        <input
                            type="checkbox"
                            name="overrides[{{ $expense->id }}][enabled]"
                            value="1"
                            {{ $isEnabled ? 'checked' : '' }}
                            class="w-4 h-4 rounded bg-zinc-800 border-zinc-600 text-indigo-600"
                            id="enabled_{{ $expense->id }}"
                        >
                    </label>
                    <div class="flex-1">
                        <label for="enabled_{{ $expense->id }}" class="font-medium text-white cursor-pointer">{{ $expense->name }}</label>
                        <p class="text-xs text-zinc-500">{{ $categoryLabels[$expense->category] ?? $expense->category }} — Montant de base : {{ number_format($expense->amount, 2, ',', ' ') }} €</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div>
                        <label class="label text-xs">Montant ce mois (€)</label>
                        <input
                            type="number"
                            name="overrides[{{ $expense->id }}][amount]"
                            value="{{ old('overrides.' . $expense->id . '.amount', $currentAmount) }}"
                            step="0.01"
                            min="0"
                            class="input w-36"
                            placeholder="{{ $expense->amount }}"
                        >
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <input
                    type="text"
                    name="overrides[{{ $expense->id }}][notes]"
                    value="{{ old('overrides.' . $expense->id . '.notes', $override?->notes ?? '') }}"
                    class="input text-xs"
                    placeholder="Notes pour ce mois (optionnel)"
                >
            </div>
        </div>
        @empty
        <div class="card text-center text-zinc-500">
            Aucune dépense récurrente. <a href="{{ route('expenses.create') }}" class="text-indigo-400 hover:underline">Créer une dépense</a>
        </div>
        @endforelse
    </div>

    @if($expenses->isNotEmpty())
    <div class="flex gap-3">
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Enregistrer
        </button>
        <a href="{{ route('expenses.index') }}" class="btn-secondary">Annuler</a>
    </div>
    @endif
</form>
@endsection
