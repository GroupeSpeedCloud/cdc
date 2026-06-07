@extends('layouts.app')
@section('title', 'Revenus')
@section('page-title', 'Revenus')

@section('content')
@php
$monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
@endphp

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Revenus</h1>
        <span class="count-badge">{{ $year }}</span>
    </div>
    <div class="year-nav">
        <a href="?year={{ $year - 1 }}" class="year-nav-btn" style="text-decoration:none;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="year-nav-value">{{ $year }}</span>
        <a href="?year={{ $year + 1 }}" class="year-nav-btn" style="text-decoration:none;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>

<div class="card-flush" style="overflow-x:auto;">
    <table class="data-table" style="min-width:900px;">
        <thead>
            <tr>
                <th style="min-width:160px;position:sticky;left:0;background:#0f0f0f;z-index:2;">Projet</th>
                @foreach($monthNames as $i => $name)
                    <th class="text-center" style="min-width:80px;">
                        <div>{{ $name }}</div>
                        <a href="{{ route('revenues.edit', [$year, $i + 1]) }}" style="font-size:10px;color:var(--accent);text-decoration:none;font-weight:400;letter-spacing:0;">Saisir</a>
                    </th>
                @endforeach
                <th class="text-right" style="min-width:100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
            <tr>
                <td style="position:sticky;left:0;background:var(--surface);z-index:1;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:10px;height:10px;border-radius:50%;background-color:{{ $project->color }};flex-shrink:0;display:block;"></span>
                        <a href="{{ route('projects.show', $project) }}" style="color:var(--text);font-weight:500;text-decoration:none;font-size:13px;transition:color 0.15s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">{{ $project->name }}</a>
                    </div>
                </td>
                @php $rowTotal = 0; @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php $amount = $grid[$project->id][$m] ?? null; $rowTotal += $amount ?? 0; @endphp
                    <td class="text-center" style="{{ $amount !== null ? 'background:#0d1f10;' : '' }}">
                        @if($amount !== null)
                            <span style="color:var(--green);font-size:12px;font-weight:600;">{{ number_format($amount, 0, ',', ' ') }} €</span>
                        @else
                            <span style="color:var(--text-3);">—</span>
                        @endif
                    </td>
                @endfor
                <td class="text-right" style="color:var(--text);font-weight:600;">{{ number_format($rowTotal, 2, ',', ' ') }} €</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="text-align:center;padding:48px 16px;color:var(--text-3);">
                    Aucun projet actif — <a href="{{ route('projects.create') }}" style="color:var(--accent);text-decoration:none;">Créer un projet</a>
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($projects->isNotEmpty())
        <tfoot>
            <tr>
                <td style="position:sticky;left:0;background:#0f0f0f;z-index:1;font-weight:700;color:var(--text);font-size:11px;text-transform:uppercase;letter-spacing:0.06em;">Total</td>
                @php $grandTotal = 0; @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php $monthTotal = collect($projects)->sum(fn($p) => $grid[$p->id][$m] ?? 0); $grandTotal += $monthTotal; @endphp
                    <td class="text-center">
                        @if($monthTotal > 0)
                            <span style="color:var(--text);font-size:12px;font-weight:700;">{{ number_format($monthTotal, 0, ',', ' ') }} €</span>
                        @else
                            <span style="color:var(--text-3);">—</span>
                        @endif
                    </td>
                @endfor
                <td class="text-right" style="font-weight:700;color:var(--text);">{{ number_format($grandTotal, 2, ',', ' ') }} €</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
