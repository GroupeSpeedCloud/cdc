@extends('layouts.app')

@section('title', 'Accès')
@section('page-title', 'Gestion des accès')

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-semibold">Comptes autorisés ({{ $emails->count() + 1 }})</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead><tr><th>Email</th><th>Statut</th><th>Ajouté le</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">{{ $superAdmin }}</td>
                            <td><span class="badge bg-primary">Super Admin</span></td>
                            <td class="text-secondary">—</td>
                            <td class="text-end text-secondary small">Protégé</td>
                        </tr>
                        @forelse($emails as $entry)
                        <tr>
                            <td>{{ $entry->email }}</td>
                            <td><span class="badge bg-success">Autorisé</span></td>
                            <td class="text-secondary">{{ $entry->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.whitelist.destroy', $entry) }}" onsubmit="return confirm('Retirer {{ $entry->email }} ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Retirer</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">Aucun compte supplémentaire</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <h6>Ajouter un compte</h6>
            <p class="text-secondary small">Le compte doit être un email {{ config('services.auth.domain', '@groupe-speed.cloud') }}</p>
            <form method="POST" action="{{ route('admin.whitelist.store') }}">@csrf
                <div class="mb-3">
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" class="form-control" placeholder="prenom.nom@groupe-speed.cloud" value="{{ old('email') }}" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Ajouter à la whitelist</button>
            </form>
            <div class="border-top mt-3 pt-3 small text-secondary">
                <strong>Note :</strong> seuls les comptes listés ici (et le super admin) peuvent se connecter.
            </div>
        </div></div>
    </div>
</div>
@endsection
