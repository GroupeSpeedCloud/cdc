<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('serviceGere')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,manager,user'],
        ]);

        $superAdmin = strtolower(config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud'));
        if (strtolower($user->email) === $superAdmin && $data['role'] !== 'admin') {
            return back()->with('error', 'Le super administrateur doit rester admin.');
        }

        // Empêche une désynchronisation rôle / responsabilité : un manager encore
        // rattaché à un service ne peut pas être rétrogradé sans que le service
        // lui soit d'abord retiré (sinon il resterait autorisé à valider des
        // factures pour ce service tout en perdant l'accès aux pages manager).
        $servicesGeres = $user->servicesGeres()->pluck('name');
        if ($data['role'] !== 'manager' && $user->role === 'manager' && $servicesGeres->isNotEmpty()) {
            return back()->with('error',
                "Impossible de retirer le rôle manager à {$user->name} : il/elle est encore responsable de ".
                $servicesGeres->implode(', ').'. Réassignez d\'abord le(s) service(s) concerné(s).');
        }

        $user->update($data);

        return back()->with('success', "Rôle de {$user->name} mis à jour.");
    }
}
