@extends('layouts.app')
@section('title', 'Modifier service')
@section('page-title', 'Modifier ' . $service->name)
@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('services.update', $service) }}">@csrf @method('PUT')
        @include('services._form')
    </form>
</div></div>
@endsection
