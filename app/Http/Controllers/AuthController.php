<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WhitelistedEmail;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

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
        // Le code d'autorisation Google est à usage unique et expire vite : un double clic,
        // un retour navigateur ou un préchargement du lien peuvent le faire échouer côté Google
        // (invalid_grant). On redirige proprement vers la connexion plutôt que de planter en 500.
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (GuzzleException|InvalidStateException $e) {
            Log::warning('Échec de la connexion Google (code invalide ou expiré).', ['message' => $e->getMessage()]);

            return redirect()->route('login')
                ->with('auth_error', 'La connexion avec Google a expiré ou a échoué. Veuillez réessayer.');
        }

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
