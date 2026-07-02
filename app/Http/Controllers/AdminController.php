<?php

namespace App\Http\Controllers;

use App\Models\WhitelistedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function isSuperAdmin(): bool
    {
        $superAdmin = strtolower(config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud'));

        return strtolower(Auth::user()->email) === $superAdmin;
    }

    public function whitelist()
    {
        abort_unless($this->isSuperAdmin(), 403);
        $emails = WhitelistedEmail::orderBy('email')->get();
        $superAdmin = config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud');

        return view('admin.whitelist', compact('emails', 'superAdmin'));
    }

    public function whitelistStore(Request $request)
    {
        abort_unless($this->isSuperAdmin(), 403);
        $request->validate([
            'email' => ['required', 'email', 'unique:whitelisted_emails,email'],
        ]);
        WhitelistedEmail::create(['email' => strtolower(trim($request->email))]);

        return back()->with('success', $request->email.' a été ajouté à la whitelist.');
    }

    public function whitelistDestroy(WhitelistedEmail $whitelistedEmail)
    {
        abort_unless($this->isSuperAdmin(), 403);
        $superAdmin = strtolower(config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud'));
        if (strtolower($whitelistedEmail->email) === $superAdmin) {
            return back()->with('error', 'Impossible de retirer le super administrateur de la whitelist.');
        }
        $whitelistedEmail->delete();

        return back()->with('success', $whitelistedEmail->email.' a été retiré de la whitelist.');
    }
}
