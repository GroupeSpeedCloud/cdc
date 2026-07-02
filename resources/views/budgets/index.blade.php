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

<form method="POST" action="{{ route('budgets.store') }}">@csrf
    <input type="hidden" name="annee" value="{{ $annee }}">
    <div class="card">
        <div class="card-header fw-semibold">Allocation des budgets — {{ $annee }}</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Service</th><th>Code</th><th class="text-end">Budget initial (€)</th><th class="text-end">Dépensé (€)</th><th class="text-end">Restant (€)</th></tr></thead>
                <tbody>
                @foreach($services as $s)
                    @php $b = $s->budgets->first(); @endphp
                    <tr>
                        <td class="fw-semibold">{{ $s->name }}</td>
                        <td><span class="badge bg-secondary">{{ $s->code }}</span></td>
                        <td class="text-end" style="max-width:180px;">
                            <input name="budgets[{{ $s->id }}]" type="number" step="0.01" min="0" class="form-control form-control-sm text-end" value="{{ old('budgets.'.$s->id, $b?->montant_initial ?? '') }}" placeholder="0.00">
                        </td>
                        <td class="text-end">{{ number_format($b?->montant_depense ?? 0, 2, ',', ' ') }} €</td>
                        <td class="text-end">{{ number_format(($b?->montant_initial ?? 0) - ($b?->montant_depense ?? 0), 2, ',', ' ') }} €</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer les budgets {{ $annee }}</button>
        </div>
    </div>
</form>
@endsection
