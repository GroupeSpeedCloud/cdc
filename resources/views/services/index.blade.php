@extends('layouts.app')
@section('title', 'Services')
@section('page-title', 'Services')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('services.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouveau service</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Nom</th><th>Code</th><th>Responsable</th><th class="text-end">Budget annuel</th><th class="text-end">Restant</th><th>Conso.</th><th></th></tr></thead>
            <tbody>
            @forelse($services as $s)
                <tr>
                    <td class="fw-semibold">{{ $s->name }}</td>
                    <td><span class="badge bg-secondary">{{ $s->code }}</span></td>
                    <td>{{ $s->manager->name ?? '—' }}</td>
                    <td class="text-end">{{ number_format($s->budget_annuel_courant, 2, ',', ' ') }} €</td>
                    <td class="text-end">{{ number_format($s->budget_restant, 2, ',', ' ') }} €</td>
                    <td style="min-width:120px;">
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar {{ $s->pourcentageConsomme() > 90 ? 'bg-danger' : 'bg-primary' }}" style="width:{{ min(100, $s->pourcentageConsomme()) }}%"></div>
                        </div>
                        <small class="text-secondary">{{ $s->pourcentageConsomme() }}%</small>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('services.edit', $s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('services.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Supprimer ce service ?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Aucun service</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
