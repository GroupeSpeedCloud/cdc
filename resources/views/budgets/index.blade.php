@extends('layouts.app')
@section('title', 'Budgets')
@section('page-title', 'Gestion budgétaire')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0">Année</label>
        <select name="annee" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            @foreach($annees as $a)
                <option value="{{ $a }}" @selected($a == $annee)>{{ $a }}{{ $a == now()->year ? ' (courante)' : ($a == now()->year+1 ? ' (N+1)' : '') }}</option>
            @endforeach
        </select>
    </form>
</div>

<form method="POST" action="{{ route('budgets.store') }}" id="budgetsForm">@csrf
    <input type="hidden" name="annee" value="{{ $annee }}">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Allocation des budgets — {{ $annee }}</span>
            <span class="small text-secondary" id="unsavedHint" style="display:none;">
                <i class="bi bi-pencil-fill text-primary"></i> Modifications non enregistrées
            </span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr>
                    <th>Service</th><th>Code</th>
                    <th class="text-end" style="width:220px;">Budget initial (€)</th>
                    <th class="text-end">Écart</th>
                    <th class="text-end">Dépensé (€)</th>
                    <th class="text-end">Crédité (€)</th>
                    <th class="text-end">Restant (€)</th>
                </tr></thead>
                <tbody>
                @foreach($services as $s)
                    @php
                        $b = $s->budgets->first();
                        $initial = (float) ($b?->montant_initial ?? 0);
                        $depense = (float) ($b?->montant_depense ?? 0);
                        $credite = (float) ($b?->montant_credite ?? 0);
                    @endphp
                    <tr class="budget-row" data-original="{{ $initial }}" data-depense="{{ $depense }}" data-credite="{{ $credite }}">
                        <td class="fw-semibold">{{ $s->name }}</td>
                        <td><span class="badge bg-secondary">{{ $s->code }}</span></td>
                        <td class="text-end">
                            <input name="budgets[{{ $s->id }}]" type="number" step="0.01" min="0"
                                   class="form-control form-control-sm text-end budget-input"
                                   value="{{ old('budgets.'.$s->id, $b?->montant_initial ?? '') }}"
                                   placeholder="0.00" oninput="onBudgetInput(this)">
                        </td>
                        <td class="text-end écart-cell">
                            <span class="badge bg-secondary-subtle text-secondary ecart-badge">—</span>
                        </td>
                        <td class="text-end text-secondary">{{ number_format($depense, 2, ',', ' ') }} €</td>
                        <td class="text-end text-success">{{ $credite > 0 ? '+'.number_format($credite, 2, ',', ' ').' €' : '—' }}</td>
                        <td class="text-end fw-semibold restant-cell">{{ number_format($initial + $credite - $depense, 2, ',', ' ') }} €</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-semibold">Total</td>
                        <td class="text-end fw-semibold" id="totalInitial">—</td>
                        <td class="text-end fw-semibold" id="totalEcart">—</td>
                        <td class="text-end fw-semibold" id="totalDepense">—</td>
                        <td class="text-end fw-semibold" id="totalCredite">—</td>
                        <td class="text-end fw-semibold" id="totalRestant">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer les budgets {{ $annee }}</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
const fmt = n => n.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' €';

function onBudgetInput(input) {
    const row = input.closest('.budget-row');
    const original = parseFloat(row.dataset.original) || 0;
    const depense = parseFloat(row.dataset.depense) || 0;
    const credite = parseFloat(row.dataset.credite) || 0;
    const value = parseFloat(input.value);
    const current = isNaN(value) ? original : value;
    const delta = current - original;

    const badge = row.querySelector('.ecart-badge');
    if (Math.abs(delta) < 0.005) {
        badge.textContent = '—';
        badge.className = 'badge bg-secondary-subtle text-secondary ecart-badge';
    } else if (delta > 0) {
        badge.textContent = '+' + fmt(delta);
        badge.className = 'badge bg-success-subtle text-success ecart-badge';
    } else {
        badge.textContent = fmt(delta);
        badge.className = 'badge bg-danger-subtle text-danger ecart-badge';
    }

    row.querySelector('.restant-cell').textContent = fmt(current + credite - depense);

    document.getElementById('unsavedHint').style.display = 'inline';
    recalcTotals();
}

function recalcTotals() {
    let totalInitial = 0, totalOriginal = 0, totalDepense = 0, totalCredite = 0, totalRestant = 0;
    document.querySelectorAll('.budget-row').forEach(row => {
        const original = parseFloat(row.dataset.original) || 0;
        const depense = parseFloat(row.dataset.depense) || 0;
        const credite = parseFloat(row.dataset.credite) || 0;
        const inputVal = parseFloat(row.querySelector('.budget-input').value);
        const current = isNaN(inputVal) ? original : inputVal;
        totalInitial += current;
        totalOriginal += original;
        totalDepense += depense;
        totalCredite += credite;
        totalRestant += current + credite - depense;
    });
    const totalDelta = totalInitial - totalOriginal;

    document.getElementById('totalInitial').textContent = fmt(totalInitial);
    document.getElementById('totalDepense').textContent = fmt(totalDepense);
    document.getElementById('totalCredite').textContent = fmt(totalCredite);
    document.getElementById('totalRestant').textContent = fmt(totalRestant);

    const totalEcartEl = document.getElementById('totalEcart');
    if (Math.abs(totalDelta) < 0.005) {
        totalEcartEl.textContent = '—';
        totalEcartEl.className = 'text-end fw-semibold text-secondary';
    } else if (totalDelta > 0) {
        totalEcartEl.textContent = '+' + fmt(totalDelta);
        totalEcartEl.className = 'text-end fw-semibold text-success';
    } else {
        totalEcartEl.textContent = fmt(totalDelta);
        totalEcartEl.className = 'text-end fw-semibold text-danger';
    }
}

recalcTotals();
</script>
@endpush
@endsection
