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

<div style="max-width:600px;">
    <div style="margin-bottom:24px;">
        <h1 class="page-title">Nouvelle dépense</h1>
        <p style="font-size:13px;color:var(--text-3);margin-top:4px;">Créez une dépense récurrente mensuelle</p>
    </div>

    <form method="POST" action="{{ route('expenses.store') }}">
        @csrf

        <div class="card" style="border-radius:16px;margin-bottom:16px;">
            <div style="margin-bottom:20px;">
                <label class="form-label">Nom <span style="color:var(--red);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Ex: Loyer local" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Catégorie <span style="color:var(--red);">*</span></label>
                    <select name="category" class="form-input" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Montant mensuel <span style="color:var(--red);">*</span></label>
                    <div class="amount-input-wrap">
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" class="amount-input" placeholder="0.00" required>
                        <span class="amount-suffix">€</span>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Début (mois d'activation) <span style="color:var(--red);">*</span></label>
                    <input type="month" name="start_month" value="{{ old('start_month', now()->format('Y-m')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Fin (optionnel)</label>
                    <input type="month" name="end_month" value="{{ old('end_month') }}" class="form-input">
                    <p style="font-size:11px;color:var(--text-3);margin-top:4px;">Laisser vide = sans fin</p>
                </div>
            </div>

            <div>
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-input" placeholder="Notes optionnelles..." style="resize:vertical;">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="padding:10px 20px;font-size:14px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Créer la dépense
            </button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
