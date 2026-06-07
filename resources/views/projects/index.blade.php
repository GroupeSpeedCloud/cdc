@extends('layouts.app')
@section('title', 'Projets')
@section('page-title', 'Projets')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-zinc-400 text-sm">{{ $projects->count() }} projet(s)</p>
    <a href="{{ route('projects.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau projet
    </a>
</div>

<div class="card overflow-hidden p-0">
    <table class="w-full text-sm">
        <thead class="bg-zinc-800/50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Projet</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Revenus ce mois</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Total cumulé</th>
                <th class="text-center px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Statut</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $item)
            <tr class="table-row">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full flex-shrink-0 border-2 border-zinc-700" style="background-color: {{ $item['project']->color }}"></span>
                        <div>
                            <a href="{{ route('projects.show', $item['project']) }}" class="font-medium text-white hover:text-indigo-400">{{ $item['project']->name }}</a>
                            @if($item['project']->description)
                                <p class="text-xs text-zinc-500 truncate max-w-xs">{{ $item['project']->description }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-right">
                    @if($item['current_revenue'] > 0)
                        <span class="text-green-400 font-medium">{{ number_format($item['current_revenue'], 2, ',', ' ') }} €</span>
                    @else
                        <span class="text-zinc-600">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <span class="text-white">{{ number_format($item['total_revenue'], 2, ',', ' ') }} €</span>
                </td>
                <td class="px-4 py-3 text-center">
                    @if($item['project']->status === 'active')
                        <span class="badge badge-green">Actif</span>
                    @else
                        <span class="badge badge-zinc">Archivé</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('projects.edit', $item['project']) }}" class="text-xs text-zinc-400 hover:text-white px-2 py-1 rounded hover:bg-zinc-800">Modifier</a>
                        <form method="POST" action="{{ route('projects.destroy', $item['project']) }}" onsubmit="return confirm('Supprimer / archiver ce projet ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-900/20">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-zinc-500">Aucun projet. <a href="{{ route('projects.create') }}" class="text-indigo-400 hover:underline">Créer le premier projet</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
