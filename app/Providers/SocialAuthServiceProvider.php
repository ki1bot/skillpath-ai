<?php

namespace App\Providers;

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SocialAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->prefix('auth')
            ->group(function () {
                Route::get(
                    '{provider}/redirect',
                    [
                        SocialAuthController::class,
                        'redirect',
                    ],
                )
                    ->middleware('guest')
                    ->whereIn(
                        'provider',
                        [
                            'google',
                            'facebook',
                        ],
                    )
                    ->name(
                        'social.redirect',
                    );

                Route::get(
                    '{provider}/callback',
                    [
                        SocialAuthController::class,
                        'callback',
                    ],
                )
                    ->whereIn(
                        'provider',
                        [
                            'google',
                            'facebook',
                        ],
                    )
                    ->name(
                        'social.callback',
                    );
            });
    }
}
