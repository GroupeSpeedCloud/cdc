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

<div style="max-width:600px;">
    <div style="margin-bottom:24px;">
        <h1 class="page-title">Modifier la dépense</h1>
        <p style="font-size:13px;color:var(--text-3);margin-top:4px;">{{ $expense->name }}</p>
    </div>

    <form method="POST" action="{{ route('expenses.update', $expense) }}">
        @csrf @method('PUT')

        <div class="card" style="border-radius:16px;margin-bottom:16px;">
            <div style="margin-bottom:20px;">
                <label class="form-label">Nom <span style="color:var(--red);">*</span></label>
                <input type="text" name="name" value="{{ old('name', $expense->name) }}" class="form-input" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Catégorie <span style="color:var(--red);">*</span></label>
                    <select name="category" class="form-input" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Montant mensuel <span style="color:var(--red);">*</span></label>
                    <div class="amount-input-wrap">
                        <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" class="amount-input" required>
                        <span class="amount-suffix">€</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Début <span style="color:var(--red);">*</span></label>
                <input type="month" name="start_month" value="{{ old('start_month', \Carbon\Carbon::parse($expense->start_month)->format('Y-m')) }}" class="form-input" required>
            </div>

            @php $isAnnual = old('repeats_annually', $expense->end_month ? '0' : '1') === '1'; @endphp

            {{-- Toggle répétition annuelle --}}
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;background:var(--surface-2);border:1px solid var(--border-2);border-radius:10px;padding:14px 16px;" onclick="toggleAnnuel(this)">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">Se répète chaque année</div>
                        <div style="font-size:12px;color:var(--text-3);margin-top:2px;">La dépense sera active indéfiniment, année après année</div>
                    </div>
                    <div id="toggle-annuel" style="width:40px;height:22px;border-radius:11px;background:{{ $isAnnual ? 'var(--accent)' : 'var(--border-2)' }};position:relative;transition:background 0.2s;flex-shrink:0;">
                        <div style="width:18px;height:18px;border-radius:50%;background:#fff;position:absolute;top:2px;{{ $isAnnual ? 'right:2px;' : 'left:2px;' }}transition:all 0.2s;"></div>
                    </div>
                </label>
                <input type="hidden" name="repeats_annually" id="repeats_annually" value="{{ $isAnnual ? '1' : '0' }}">
            </div>

            {{-- Champ fin --}}
            <div id="end-month-wrap" style="display:{{ $isAnnual ? 'none' : 'block' }};margin-bottom:20px;">
                <label class="form-label">Mois de fin</label>
                <input type="month" name="end_month" value="{{ old('end_month', $expense->end_month ? \Carbon\Carbon::parse($expense->end_month)->format('Y-m') : '') }}" class="form-input" id="end_month">
                <p style="font-size:11px;color:var(--text-3);margin-top:4px;">La dépense sera désactivée après ce mois</p>
            </div>

            <div>
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-input" style="resize:vertical;">{{ old('notes', $expense->notes) }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="padding:10px 20px;font-size:14px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleAnnuel(label) {
    const toggle = document.getElementById('toggle-annuel');
    const dot = toggle.querySelector('div');
    const input = document.getElementById('repeats_annually');
    const endWrap = document.getElementById('end-month-wrap');
    const endInput = document.getElementById('end_month');
    const isOn = input.value === '1';

    if (isOn) {
        input.value = '0';
        toggle.style.background = 'var(--border-2)';
        dot.style.right = 'auto';
        dot.style.left = '2px';
        endWrap.style.display = 'block';
    } else {
        input.value = '1';
        toggle.style.background = 'var(--accent)';
        dot.style.left = 'auto';
        dot.style.right = '2px';
        endWrap.style.display = 'none';
        endInput.value = '';
    }
}
</script>
@endpush
@endsection
