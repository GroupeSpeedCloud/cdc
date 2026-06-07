@extends('layouts.app')
@section('title', 'Nouvelle dépense récurrente')
@section('page-title', 'Nouvelle dépense récurrente')

@section('content')
@php
$categories = [
    'personnel' => 'Personnel',
    'hebergement' => 'Hébergement',
    'infrastructure' => 'Infrastructure',
    'marketing' => 'Marketing',
    'locaux' => 'Locaux',
    'autre' => 'Autre',
];
@endphp

<div class="max-w-xl">
    <form method="POST" action="{{ route('expenses.store') }}" class="space-y-5">
        @csrf

        <div class="card space-y-4">
            <div>
                <label class="label">Nom <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input" placeholder="Ex: Loyer local" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Catégorie <span class="text-red-400">*</span></label>
                    <select name="category" class="input" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Montant mensuel (€) <span class="text-red-400">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" class="input" placeholder="0.00" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Début (mois d'activation) <span class="text-red-400">*</span></label>
                    <input type="month" name="start_month" value="{{ old('start_month', now()->format('Y-m')) }}" class="input" required>
                </div>

                <div>
                    <label class="label">Fin (optionnel)</label>
                    <input type="month" name="end_month" value="{{ old('end_month') }}" class="input">
                    <p class="text-xs text-zinc-500 mt-1">Laisser vide = sans fin</p>
                </div>
            </div>

            <div>
                <label class="label">Notes</label>
                <textarea name="notes" rows="2" class="input" placeholder="Notes optionnelles...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Créer la dépense</button>
            <a href="{{ route('expenses.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
