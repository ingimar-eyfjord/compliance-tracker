<?php

use App\Http\Controllers\Settings\AuditLogController;
use App\Http\Controllers\Settings\FeatureToggleController;
use App\Http\Controllers\Settings\MemberController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('settings/members', [MemberController::class, 'index'])
        ->name('settings.members.index');

    Route::get('settings/features', [FeatureToggleController::class, 'index'])
        ->name('settings.features.index');

    Route::get('settings/audit', [AuditLogController::class, 'index'])
        ->middleware('feature:compliance')
        ->name('settings.audit.index');
});
