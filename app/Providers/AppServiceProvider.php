<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partage les notifications de l'utilisateur avec toutes les vues (topbar).
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
            $notifications = collect();
            $nonLues = 0;
            if ($user) {
                $notifications = $user->appNotifications()->take(8)->get();
                $nonLues = $user->unreadNotificationsCount();
            }
            $view->with('topbarNotifications', $notifications)
                ->with('topbarNonLues', $nonLues);
        });
    }
}
