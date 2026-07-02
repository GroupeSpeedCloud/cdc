@extends('layouts.app')
@section('title', 'Nouveau service')
@section('page-title', 'Nouveau service')
@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('services.store') }}">@csrf
        @include('services._form')
    </form>
</div></div>
@endsection
