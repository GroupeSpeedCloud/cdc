@extends('layouts.app')
@section('title', 'Compte — ' . $service->name)
@section('page-title', 'Compte du service')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">{{ $service->name }} <span class="badge bg-secondary">{{ $service->code }}</span></h4>
        <div class="text-secondary small font-monospace">{{ $service->numeroCompte() }}</div>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour aux services</a>
    @endif
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-4">
        <div class="card h-100" style="background:linear-gradient(135deg,#8a4dfd,#6d28d9);color:#fff;">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="small text-uppercase" style="opacity:.8;letter-spacing:.05em;font-size:.7rem;">Solde actuel</div>
                <div class="fs-2 fw-bold">{{ number_format($service->budget_restant, 2, ',', ' ') }} €</div>
                <div class="small mt-2" style="opacity:.85;">
                    Compte courant interne · Titulaire {{ $service->manager->name ?? '—' }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100"><div class="card-body d-flex flex-column justify-content-center">
            <div class="text-secondary small text-uppercase" style="font-size:.7rem;">Total crédité</div>
            <div class="fs-4 fw-bold text-success">+{{ number_format($totalCredits, 2, ',', ' ') }} €</div>
            <div class="text-secondary small mt-1">Facturation émise vers d'autres services</div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100"><div class="card-body d-flex flex-column justify-content-center">
            <div class="text-secondary small text-uppercase" style="font-size:.7rem;">Total débité</div>
            <div class="fs-4 fw-bold text-danger">-{{ number_format($totalDebits, 2, ',', ' ') }} €</div>
            <div class="text-secondary small mt-1">Factures payées à d'autres services</div>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-semibold">Relevé de compte</span>
        <form method="GET" class="d-flex align-items-center gap-2">
            <select name="annee" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="0" @selected($annee === 0)>Toutes les années</option>
                @foreach($annees as $a)
                    <option value="{{ $a }}" @selected($annee === $a)>{{ $a }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr>
                <th>Date</th><th>Référence</th><th>Libellé</th><th>Contrepartie</th>
                <th class="text-end">Débit</th><th class="text-end">Crédit</th><th class="text-end">Solde</th>
            </tr></thead>
            <tbody>
            @forelse($mouvements as $m)
                <tr>
                    <td class="text-secondary">{{ optional($m['date'])->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('documents.show', $m['document']) }}" class="text-decoration-none">
                            {{ $m['document']->numero_document }}
                        </a>
                    </td>
                    <td>{{ $m['libelle'] }}</td>
                    <td class="text-secondary">{{ $m['contrepartie']->name ?? '—' }}</td>
                    <td class="text-end text-danger">{{ $m['debit'] ? '-'.number_format($m['debit'], 2, ',', ' ').' €' : '' }}</td>
                    <td class="text-end text-success">{{ $m['credit'] ? '+'.number_format($m['credit'], 2, ',', ' ').' €' : '' }}</td>
                    <td class="text-end fw-semibold">{{ number_format($m['solde_apres'], 2, ',', ' ') }} €</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Aucune opération</td></tr>
            @endforelse
            </tbody>
            @if($mouvements->isNotEmpty())
            <tfoot>
                <tr class="table-light">
                    <td colspan="6" class="text-end text-secondary">Solde avant ces opérations</td>
                    <td class="text-end text-secondary">{{ number_format($soldeOuverture, 2, ',', ' ') }} €</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
