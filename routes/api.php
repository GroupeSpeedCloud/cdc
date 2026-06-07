<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinanceController;

Route::middleware(['auth:sanctum', 'restrict.domain'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/finances/kpi', [FinanceController::class, 'kpi']);
});
