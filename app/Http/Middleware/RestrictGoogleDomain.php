<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\WhitelistedEmail;

class RestrictGoogleDomain
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        // Vérification du domaine
        $domain = config('services.auth.domain', '@groupe-speed.cloud');
        if (!Str::endsWith($user->email, $domain)) {
            Auth::logout();
            return redirect()->route('forbidden')->with('blocked_email', $user->email);
        }

        if (!WhitelistedEmail::isAllowed($user->email)) {
            $email = $user->email;
            Auth::logout();
            return redirect()->route('forbidden')->with('blocked_email', $email);
        }

        return $next($request);
    }
}
