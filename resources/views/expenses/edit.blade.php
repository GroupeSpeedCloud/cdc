@extends('layouts.app')
@section('title', 'Modifier la dépense')
@section('page-title', 'Modifier la dépense')

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
    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="card space-y-4">
            <div>
                <label class="label">Nom <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $expense->name) }}" class="input" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Catégorie <span class="text-red-400">*</span></label>
                    <select name="category" class="input" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Montant mensuel (€) <span class="text-red-400">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" class="input" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Début <span class="text-red-400">*</span></label>
                    <input type="month" name="start_month" value="{{ old('start_month', \Carbon\Carbon::parse($expense->start_month)->format('Y-m')) }}" class="input" required>
                </div>

                <div>
                    <label class="label">Fin (optionnel)</label>
                    <input type="month" name="end_month" value="{{ old('end_month', $expense->end_month ? \Carbon\Carbon::parse($expense->end_month)->format('Y-m') : '') }}" class="input">
                </div>
            </div>

            <div>
                <label class="label">Notes</label>
                <textarea name="notes" rows="2" class="input">{{ old('notes', $expense->notes) }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Enregistrer</button>
            <a href="{{ route('expenses.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
