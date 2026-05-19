@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Intelligence Artificielle Financière</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-card>
            <form method="POST" action="{{ route('ai.summary') }}" class="space-y-4">
                @csrf
                <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded w-full">Générer un résumé IA automatique</button>
            </form>
        </x-card>
        <x-card>
            <form method="POST" action="{{ route('ai.analyze') }}" class="space-y-4">
                @csrf
                <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded w-full">Analyser automatiquement les finances</button>
            </form>
        </x-card>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card>
            <form method="POST" action="{{ route('ai.anomalies') }}" class="space-y-4">
                @csrf
                <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded w-full">Détecter automatiquement les anomalies</button>
            </form>
        </x-card>
        <x-card>
            <div class="text-zinc-400 text-sm">
                <p>L’IA analyse et prédit automatiquement à partir de toutes vos données financières. Aucun contexte manuel à fournir.</p>
            </div>
        </x-card>
    </div>
</div>
@endsection
