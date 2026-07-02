@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs & rôles')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Nom</th><th>Email</th><th>Service géré</th><th style="width:280px;">Rôle</th></tr></thead>
            <tbody>
            @foreach($users as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->serviceGere->name ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('users.update', $u) }}" class="d-flex gap-2">@csrf @method('PUT')
                            <select name="role" class="form-select form-select-sm">
                                @foreach(['admin','manager','user'] as $r)
                                    <option value="{{ $r }}" @selected($u->role === $r)>{{ ucfirst($r) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-primary">OK</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
