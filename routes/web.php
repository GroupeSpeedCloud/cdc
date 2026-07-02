<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentInterneController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonneController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forbidden', fn () => view('auth.forbidden'))->name('forbidden');

Route::middleware(['auth', 'restrict.domain'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Documents internes (cœur du système)
    Route::get('documents/{document}/pdf', [DocumentInterneController::class, 'pdf'])->name('documents.pdf');
    Route::post('documents/{document}/soumettre', [DocumentInterneController::class, 'soumettre'])->name('documents.soumettre');
    Route::post('documents/{document}/valider', [DocumentInterneController::class, 'valider'])->name('documents.valider');
    Route::post('documents/{document}/refuser', [DocumentInterneController::class, 'refuser'])->name('documents.refuser');
    Route::post('documents/{document}/archiver', [DocumentInterneController::class, 'archiver'])->name('documents.archiver');
    Route::resource('documents', DocumentInterneController::class);

    // Personnes (admin + manager)
    Route::resource('personnes', PersonneController::class)->except(['show'])
        ->middleware('role:admin,manager');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/tout-lire', [NotificationController::class, 'toutLire'])->name('notifications.toutLire');
    Route::get('notifications/{notification}', [NotificationController::class, 'lire'])->name('notifications.lire');

    // Rapports (admin + manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Administration (admin uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::get('budgets', [BudgetController::class, 'index'])->name('budgets.index');
        Route::post('budgets', [BudgetController::class, 'store'])->name('budgets.store');
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    // Whitelist (super admin)
    Route::get('admin/whitelist', [AdminController::class, 'whitelist'])->name('admin.whitelist');
    Route::post('admin/whitelist', [AdminController::class, 'whitelistStore'])->name('admin.whitelist.store');
    Route::delete('admin/whitelist/{whitelistedEmail}', [AdminController::class, 'whitelistDestroy'])->name('admin.whitelist.destroy');
});
