@extends('layouts.app')
@section('title', 'Rapport annuel')
@section('page-title', 'Rapport annuel')

@section('content')
@php
$monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
@endphp

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Rapport annuel</h1>
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

{{-- Summary KPIs --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="kpi-card">
        <span class="kpi-label">Revenus {{ $year }}</span>
        <div class="kpi-value" style="margin-top:10px;color:var(--green);">{{ number_format($summary['total_revenue'], 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;opacity:0.7;">€</span></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Dépenses {{ $year }}</span>
        <div class="kpi-value" style="margin-top:10px;color:var(--red);">{{ number_format($summary['total_expenses'], 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;opacity:0.7;">€</span></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Profit net {{ $year }}</span>
        <div class="kpi-value" style="margin-top:10px;color:{{ $summary['total_profit'] >= 0 ? 'var(--green)' : 'var(--red)' }}">
            {{ $summary['total_profit'] >= 0 ? '+' : '' }}{{ number_format($summary['total_profit'], 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;opacity:0.7;">€</span>
        </div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Marge moyenne</span>
        <div class="kpi-value" style="margin-top:10px;">{{ $summary['avg_margin'] }}<span style="font-size:18px;font-weight:500;color:var(--text-3);">%</span></div>
    </div>
</div>

{{-- Monthly table --}}
<div class="card-flush">
    <table class="data-table">
        <thead>
            <tr>
                <th>Mois</th>
                <th class="text-right" style="color:rgba(16,185,129,0.7);">Revenus</th>
                <th class="text-right" style="color:rgba(239,68,68,0.7);">Dépenses</th>
                <th class="text-right">Profit net</th>
                <th class="text-right">Marge</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['months'] as $m => $data)
            <tr>
                <td style="color:var(--text);font-weight:500;">{{ $monthNames[$m] }}</td>
                <td class="text-right">
                    @if($data['revenue'] > 0)
                        <span style="color:var(--green);font-weight:500;">{{ number_format($data['revenue'], 2, ',', ' ') }} €</span>
                    @else
                        <span style="color:var(--text-3);">—</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($data['expenses'] > 0)
                        <span style="color:var(--red);">{{ number_format($data['expenses'], 2, ',', ' ') }} €</span>
                    @else
                        <span style="color:var(--text-3);">—</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($data['revenue'] > 0 || $data['expenses'] > 0)
                        <span style="color:{{ $data['profit'] >= 0 ? 'var(--green)' : 'var(--red)' }};font-weight:600;">
                            {{ $data['profit'] >= 0 ? '+' : '' }}{{ number_format($data['profit'], 2, ',', ' ') }} €
                        </span>
                    @else
                        <span style="color:var(--text-3);">—</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($data['revenue'] > 0)
                        <span style="color:{{ $data['margin'] >= 0 ? 'var(--green)' : 'var(--red)' }};">{{ $data['margin'] }}%</span>
                    @else
                        <span style="color:var(--text-3);">—</span>
                    @endif
                </td>
                <td class="text-center">
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                        <a href="{{ route('revenues.edit', [$year, $m]) }}" style="font-size:11px;color:var(--accent);text-decoration:none;padding:3px 8px;border-radius:6px;background:var(--accent-bg);transition:opacity 0.15s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">Revenus</a>
                        <a href="{{ route('expenses.override', [$year, $m]) }}" style="font-size:11px;color:var(--text-3);text-decoration:none;padding:3px 8px;border-radius:6px;background:var(--surface-2);transition:color 0.15s;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-3)'">Dépenses</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:linear-gradient(90deg, rgba(99,102,241,0.05) 0%, rgba(99,102,241,0.02) 100%);">
                <td style="font-weight:700;color:var(--text);font-size:12px;text-transform:uppercase;letter-spacing:0.06em;">Total {{ $year }}</td>
                <td class="text-right" style="font-weight:700;color:var(--green);">{{ number_format($summary['total_revenue'], 2, ',', ' ') }} €</td>
                <td class="text-right" style="font-weight:700;color:var(--red);">{{ number_format($summary['total_expenses'], 2, ',', ' ') }} €</td>
                <td class="text-right" style="font-weight:700;color:{{ $summary['total_profit'] >= 0 ? 'var(--green)' : 'var(--red)' }}">
                    {{ $summary['total_profit'] >= 0 ? '+' : '' }}{{ number_format($summary['total_profit'], 2, ',', ' ') }} €
                </td>
                <td class="text-right" style="font-weight:700;color:var(--text);">{{ $summary['avg_margin'] }}%</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
