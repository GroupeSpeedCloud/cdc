@extends('layouts.app')
@section('title', 'Accès refusé')
@section('page-title', 'Accès refusé')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card" style="max-width:520px;width:100%;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="bi bi-shield-lock text-danger" style="font-size:2.5rem;"></i>
            </div>
            <h5 class="mb-2">Vous n'avez pas accès à cette page</h5>
            <p class="text-secondary mb-4">
                {{ $exception->getMessage() ?: "Votre rôle actuel ne permet pas cette action. Si vous pensez que c'est une erreur, contactez un administrateur." }}
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="bi bi-house"></i> Retour au tableau de bord</a>
        </div>
    </div>
</div>
@endsection
