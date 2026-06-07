@extends('layouts.app')

@section('title', 'Accès — Administration')
@section('page-title', 'Gestion des accès')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div style="width:36px;height:36px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <div class="page-title">Whitelist d'accès</div>
            <div class="page-subtitle">Comptes Google autorisés à se connecter</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    <!-- Liste -->
    <div class="card-flush">
        <div class="card-header">
            <div>
                <div class="card-title">Comptes autorisés</div>
                <div class="card-subtitle">{{ $emails->count() + 1 }} compte(s) au total</div>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Ajouté le</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Super admin (toujours présent) -->
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;background:rgba(99,102,241,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:600;color:var(--accent);">
                                {{ strtoupper(substr($superAdmin, 0, 1)) }}
                            </div>
                            <span style="color:var(--text);font-size:13px;">{{ $superAdmin }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-indigo">
                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Super Admin
                        </span>
                    </td>
                    <td style="color:var(--text-3);font-size:12px;">—</td>
                    <td class="text-right">
                        <span style="font-size:11px;color:var(--text-3);">Protégé</span>
                    </td>
                </tr>
                @forelse($emails as $entry)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;background:var(--surface-2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:600;color:var(--text-2);">
                                {{ strtoupper(substr($entry->email, 0, 1)) }}
                            </div>
                            <span style="color:var(--text);font-size:13px;">{{ $entry->email }}</span>
                        </div>
                    </td>
                    <td><span class="badge badge-green">Autorisé</span></td>
                    <td style="color:var(--text-3);font-size:12px;">{{ $entry->created_at->format('d/m/Y') }}</td>
                    <td class="text-right">
                        <form method="POST" action="{{ route('admin.whitelist.destroy', $entry) }}" onsubmit="return confirm('Retirer {{ $entry->email }} de la whitelist ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost-red">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Retirer
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--text-3);padding:32px 16px;font-size:13px;">
                        Aucun compte supplémentaire dans la whitelist
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Formulaire ajout -->
    <div class="card">
        <div style="margin-bottom:20px;">
            <div class="card-title" style="margin-bottom:4px;">Ajouter un compte</div>
            <div style="font-size:12px;color:var(--text-3);">Le compte doit être un email @{{ ltrim(config('services.auth.domain', '@groupe-speed.cloud'), '@') }}</div>
        </div>
        <form method="POST" action="{{ route('admin.whitelist.store') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label class="form-label">Adresse email</label>
                <input type="email" name="email" class="form-input" placeholder="prenom.nom@groupe-speed.cloud" value="{{ old('email') }}" required>
                @error('email')
                    <div style="font-size:12px;color:var(--red);margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Ajouter à la whitelist
            </button>
        </form>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="font-size:11px;color:var(--text-3);line-height:1.6;">
                <strong style="color:var(--text-2);">Note :</strong> Seuls les comptes listés ici (et le super admin) peuvent se connecter à Flow. Tout autre compte sera redirigé vers la page d'accès refusé.
            </div>
        </div>
    </div>

</div>
@endsection
