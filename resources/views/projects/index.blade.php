@extends('layouts.app')
@section('title', 'Projets')
@section('page-title', 'Projets')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Projets</h1>
        <span class="count-badge">{{ $projects->count() }}</span>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nouveau projet
    </a>
</div>

<div class="card-flush">
    <table class="data-table">
        <thead>
            <tr>
                <th>Projet</th>
                <th class="text-right">Revenus ce mois</th>
                <th class="text-right">Total cumulé</th>
                <th class="text-center">Statut</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $item)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="width:12px;height:12px;border-radius:50%;background-color:{{ $item['project']->color }};flex-shrink:0;display:block;"></span>
                        <div>
                            <a href="{{ route('projects.show', $item['project']) }}" style="color:var(--text);font-weight:500;font-size:13px;text-decoration:none;transition:color 0.15s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">{{ $item['project']->name }}</a>
                            @if($item['project']->description)
                                <p style="font-size:12px;color:var(--text-3);margin-top:2px;max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['project']->description }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    @if($item['current_revenue'] > 0)
                        <span style="color:var(--green);font-weight:600;">{{ number_format($item['current_revenue'], 2, ',', ' ') }} €</span>
                    @else
                        <span style="color:var(--text-3);">—</span>
                    @endif
                </td>
                <td class="text-right" style="color:var(--text);font-weight:500;">{{ number_format($item['total_revenue'], 2, ',', ' ') }} €</td>
                <td class="text-center">
                    @if($item['project']->status === 'active')
                        <span class="badge badge-green">Actif</span>
                    @else
                        <span class="badge badge-muted">Archivé</span>
                    @endif
                </td>
                <td class="text-right">
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px;">
                        <a href="{{ route('projects.edit', $item['project']) }}" class="btn btn-ghost" title="Modifier">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('projects.destroy', $item['project']) }}" onsubmit="return confirm('Supprimer / archiver ce projet ?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost-red" title="Supprimer">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:48px 16px;color:var(--text-3);">
                    Aucun projet — <a href="{{ route('projects.create') }}" style="color:var(--accent);text-decoration:none;">Créer le premier projet</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
