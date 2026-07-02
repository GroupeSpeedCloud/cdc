@extends('layouts.app')
@section('title', 'Personnes')
@section('page-title', 'Personnes')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('personnes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Ajouter une personne</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Nom</th><th>Compte lié</th><th>Service</th><th class="text-end">Tarif horaire</th><th></th></tr></thead>
            <tbody>
            @forelse($personnes as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->nomAffiche() }}</td>
                    <td>{{ $p->user->email ?? '—' }}</td>
                    <td>{{ $p->service->name ?? '—' }}</td>
                    <td class="text-end">{{ number_format($p->tarif_horaire_par_defaut, 2, ',', ' ') }} €/h</td>
                    <td class="text-end">
                        <a href="{{ route('personnes.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('personnes.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Aucune personne</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
