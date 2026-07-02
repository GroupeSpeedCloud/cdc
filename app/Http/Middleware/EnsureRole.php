<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Autorise l'accès si l'utilisateur possède l'un des rôles fournis.
     * Usage: ->middleware('role:admin') ou 'role:admin,manager'
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, "Vous n'avez pas les droits nécessaires pour accéder à cette page.");
        }

        return $next($request);
    }
}
