@extends('layouts.app')
@section('title', 'Revenus')
@section('page-title', 'Revenus')

@section('content')
@php
$monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
@endphp

<div class="flex items-center gap-4 mb-6">
    <form method="GET" class="flex items-center gap-2">
        <label class="text-sm text-zinc-400">Année :</label>
        <select name="year" onchange="this.form.submit()" class="input w-auto">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card overflow-x-auto p-0">
    <table class="w-full text-sm min-w-max">
        <thead class="bg-zinc-800/50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase sticky left-0 bg-zinc-800/50">Projet</th>
                @foreach($monthNames as $i => $name)
                    <th class="text-center px-3 py-3 text-xs font-medium text-zinc-500 uppercase min-w-[90px]">
                        <div>{{ $name }}</div>
                        <a href="{{ route('revenues.edit', [$year, $i + 1]) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-normal">Saisir</a>
                    </th>
                @endforeach
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
            <tr class="table-row">
                <td class="px-4 py-3 sticky left-0 bg-zinc-900">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $project->color }}"></span>
                        <a href="{{ route('projects.show', $project) }}" class="text-white hover:text-indigo-400 font-medium">{{ $project->name }}</a>
                    </div>
                </td>
                @php $rowTotal = 0; @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php $amount = $grid[$project->id][$m] ?? null; $rowTotal += $amount ?? 0; @endphp
                    <td class="px-3 py-3 text-center">
                        @if($amount !== null)
                            <span class="text-green-400 text-xs font-medium">{{ number_format($amount, 0, ',', ' ') }} €</span>
                        @else
                            <span class="text-zinc-700">—</span>
                        @endif
                    </td>
                @endfor
                <td class="px-4 py-3 text-right">
                    <span class="text-white font-medium">{{ number_format($rowTotal, 2, ',', ' ') }} €</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="14" class="px-4 py-8 text-center text-zinc-500">Aucun projet actif. <a href="{{ route('projects.create') }}" class="text-indigo-400 hover:underline">Créer un projet</a></td></tr>
            @endforelse
        </tbody>
        @if($projects->isNotEmpty())
        <tfoot class="bg-zinc-800/30 border-t border-zinc-700">
            <tr>
                <td class="px-4 py-3 text-xs font-semibold text-zinc-300 uppercase sticky left-0 bg-zinc-800/30">Total</td>
                @php $grandTotal = 0; @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php $monthTotal = collect($projects)->sum(fn($p) => $grid[$p->id][$m] ?? 0); $grandTotal += $monthTotal; @endphp
                    <td class="px-3 py-3 text-center">
                        @if($monthTotal > 0)
                            <span class="text-zinc-300 text-xs font-medium">{{ number_format($monthTotal, 0, ',', ' ') }} €</span>
                        @else
                            <span class="text-zinc-700">—</span>
                        @endif
                    </td>
                @endfor
                <td class="px-4 py-3 text-right text-white font-bold">{{ number_format($grandTotal, 2, ',', ' ') }} €</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
