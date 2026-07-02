@extends('layouts.app')
@section('title', 'Documents')
@section('page-title', 'Documents internes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach($statuts as $s)
                <option value="{{ $s }}" @selected(request('statut') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouveau document</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr>
                <th>N°</th><th>Émetteur</th><th>Destinataire</th><th>Demandeur</th>
                <th>Date</th><th class="text-end">Total TTC</th><th>Statut</th>
            </tr></thead>
            <tbody>
            @forelse($documents as $doc)
                <tr onclick="window.location='{{ route('documents.show', $doc) }}'" style="cursor:pointer;">
                    <td class="fw-semibold">{{ $doc->numero_document }}</td>
                    <td>{{ $doc->serviceEmetteur->name }}</td>
                    <td>{{ $doc->serviceDestinataire->name }}</td>
                    <td>{{ $doc->demandeur->name }}</td>
                    <td>{{ $doc->date_emission->format('d/m/Y') }}</td>
                    <td class="text-end">{{ $doc->montantTtcFormate() }}</td>
                    <td><span class="badge {{ $doc->statutBadgeClass() }}">{{ $doc->statut }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Aucun document</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $documents->links() }}</div>
@endsection
