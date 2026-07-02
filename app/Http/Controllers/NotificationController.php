<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->appNotifications()->with('document')->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    public function lire(AppNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->update(['lu' => true]);

        if ($notification->document_interne_id) {
            return redirect()->route('documents.show', $notification->document_interne_id);
        }

        return back();
    }

    public function toutLire()
    {
        Auth::user()->appNotifications()->where('lu', false)->update(['lu' => true]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
