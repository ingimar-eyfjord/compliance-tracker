<?php

use App\Http\Controllers\BreachLogController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ComplianceOverviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('channels', ChannelController::class);
    Route::resource('cases', CaseController::class);
    Route::resource('reports', ReportController::class);

    Route::middleware('feature:compliance')->group(function () {
        Route::resource('breaches', BreachLogController::class)->parameters([
            'breaches' => 'breach',
        ]);
        Route::get('compliance/overview', ComplianceOverviewController::class)->name('compliance.overview');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
