<?php

use App\Http\Controllers\Settings\EmailVerificationController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'idle'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get(
        'settings/profile',
        [ProfileController::class, 'edit'],
    )->name('profile.edit');

    Route::patch(
        'settings/profile',
        [ProfileController::class, 'update'],
    )->name('profile.update');

    Route::get(
        'settings/profile/verify-email',
        [EmailVerificationController::class, 'show'],
    )->name('email-verification.show');

    Route::post(
        'settings/profile/verify-email/send',
        [EmailVerificationController::class, 'send'],
    )
        ->middleware('throttle:3,1')
        ->name('email-verification.send');

    Route::post(
        'settings/profile/verify-email',
        [EmailVerificationController::class, 'verify'],
    )
        ->middleware('throttle:10,1')
        ->name('email-verification.verify');

    Route::delete(
        'settings/profile',
        [ProfileController::class, 'destroy'],
    )->name('profile.destroy');

    Route::get(
        'settings/security',
        [SecurityController::class, 'edit'],
    )
        ->middleware(RequirePassword::class)
        ->name('security.edit');

    Route::put(
        'settings/password',
        [SecurityController::class, 'update'],
    )
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia(
        'settings/appearance',
        'settings/appearance',
    )->name('appearance.edit');
});
