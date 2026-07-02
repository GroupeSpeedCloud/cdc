<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WhitelistedEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirige vers Google pour l'authentification.
     */
    public function redirectToGoogle()
    {
        // If there's an auth error and no force flag, show the login page with error
        if (session('auth_error') && ! request('force')) {
            return view('auth.login', ['error' => session('auth_error')]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Gère le callback Google et la restriction de domaine.
     */
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $email = $googleUser->getEmail();

        $domain = config('services.auth.domain', '@groupe-speed.cloud');
        if (! Str::endsWith($email, $domain)) {
            return redirect('/login')->with('auth_error', 'Seuls les comptes '.$domain.' sont autorisés.');
        }

        if (! WhitelistedEmail::isAllowed($email)) {
            return redirect()->route('forbidden')->with('blocked_email', $email);
        }

        $superAdmin = strtolower(config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud'));
        $isSuperAdmin = strtolower($email) === $superAdmin;

        $user = User::firstOrCreate([
            'email' => $email,
        ], [
            'name' => $googleUser->getName(),
            'role' => $isSuperAdmin ? 'admin' : 'user',
            'password' => bcrypt(Str::random(32)),
        ]);

        // Le super admin est toujours promu admin.
        if ($isSuperAdmin && $user->role !== 'admin') {
            $user->update(['role' => 'admin']);
        }

        Auth::login($user, true);

        return redirect('/dashboard');
    }

    /**
     * Déconnexion sécurisée.
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
