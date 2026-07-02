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

        $user->update($data);

        return back()->with('success', "Rôle de {$user->name} mis à jour.");
    }
}
