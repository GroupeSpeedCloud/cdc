@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <form method="POST" action="{{ route('notifications.toutLire') }}">@csrf
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check2-all"></i> Tout marquer comme lu</button>
    </form>
</div>
<div class="card">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notif)
            <a href="{{ route('notifications.lire', $notif) }}" class="list-group-item list-group-item-action d-flex gap-3 {{ $notif->lu ? '' : 'fw-semibold' }}">
                <i class="bi {{ $notif->type === 'validation' ? 'bi-check-circle text-success' : ($notif->type === 'refus' ? 'bi-x-circle text-danger' : 'bi-info-circle text-primary') }} fs-5"></i>
                <div class="flex-grow-1">
                    {{ $notif->message }}
                    <div class="text-secondary small">{{ $notif->created_at->diffForHumans() }}</div>
                </div>
                @unless($notif->lu)<span class="badge bg-primary align-self-center">Nouveau</span>@endunless
            </a>
        @empty
            <div class="text-center text-secondary py-5">Aucune notification</div>
        @endforelse
    </div>
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
