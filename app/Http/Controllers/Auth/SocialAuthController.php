<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    private const PROVIDERS = [
        'google',
        'facebook',
    ];

    public function redirect(
        Request $request,
        string $provider,
    ): SymfonyRedirectResponse {
        $provider = $this->validateProvider(
            $provider,
        );

        $source = $request->query(
            'source',
        ) === 'register'
            ? 'register'
            : 'login';

        $request->session()->put(
            'social-auth.source',
            $source,
        );

        return Socialite::driver(
            $provider,
        )->redirect();
    }

    public function callback(
        Request $request,
        string $provider,
    ): RedirectResponse {
        $provider = $this->validateProvider(
            $provider,
        );

        try {
            $providerUser = Socialite::driver(
                $provider,
            )->user();
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed(
                $request,
                'Otentikasi dengan '
                .$this->providerLabel($provider)
                .' gagal atau dibatalkan. Silakan coba lagi.',
            );
        }

        $providerUserId = trim(
            (string) $providerUser->getId(),
        );

        if ($providerUserId === '') {
            return $this->failed(
                $request,
                'Akun '
                .$this->providerLabel($provider)
                .' tidak mengirimkan identitas pengguna yang valid.',
            );
        }

        $existingSocialAccount = SocialAccount::query()
            ->with('user')
            ->where(
                'provider',
                $provider,
            )
            ->where(
                'provider_user_id',
                $providerUserId,
            )
            ->first();

        if (
            $existingSocialAccount !== null
            && $existingSocialAccount->user instanceof User
        ) {
            $this->authenticate(
                $request,
                $existingSocialAccount->user,
            );

            $request->session()->forget(
                'social-auth.source',
            );

            return redirect()->intended(
                route('dashboard'),
            );
        }

        $email = Str::lower(
            trim(
                (string) $providerUser->getEmail(),
            ),
        );

        if ($email === '') {
            return $this->failed(
                $request,
                'Akun '
                .$this->providerLabel($provider)
                .' tidak memberikan alamat email. Pastikan izin email diberikan lalu coba lagi.',
            );
        }

        $name = trim(
            (string) $providerUser->getName(),
        );

        if ($name === '') {
            $name = Str::before(
                $email,
                '@',
            );
        }

        try {
            $user = DB::transaction(
                function () use (
                    $provider,
                    $providerUserId,
                    $email,
                    $name,
                ): User {
                    $socialAccount = SocialAccount::query()
                        ->with('user')
                        ->where(
                            'provider',
                            $provider,
                        )
                        ->where(
                            'provider_user_id',
                            $providerUserId,
                        )
                        ->lockForUpdate()
                        ->first();

                    if (
                        $socialAccount !== null
                        && $socialAccount->user instanceof User
                    ) {
                        return $socialAccount->user;
                    }

                    $user = User::query()
                        ->whereRaw(
                            'LOWER(email) = ?',
                            [$email],
                        )
                        ->lockForUpdate()
                        ->first();

                    if ($user === null) {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => Str::random(64),
                            'role' => 'student',
                        ]);
                    }

                    if (
                        $user->email_verified_at === null
                    ) {
                        $user
                            ->forceFill([
                                'email_verified_at' => now(),
                            ])
                            ->save();
                    }

                    SocialAccount::query()
                        ->updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'provider' => $provider,
                            ],
                            [
                                'provider_user_id' => $providerUserId,
                            ],
                        );

                    return $user;
                },
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed(
                $request,
                'SkillPath AI gagal menyimpan akun '
                .$this->providerLabel($provider)
                .'. Silakan coba lagi.',
            );
        }

        $this->authenticate(
            $request,
            $user,
        );

        $request->session()->forget(
            'social-auth.source',
        );

        return redirect()->intended(
            route('dashboard'),
        );
    }

    private function authenticate(
        Request $request,
        User $user,
    ): void {
        Auth::login(
            $user,
        );

        $request
            ->session()
            ->regenerate();
    }

    private function failed(
        Request $request,
        string $message,
    ): RedirectResponse {
        $source = $request
            ->session()
            ->pull(
                'social-auth.source',
                'login',
            );

        $route = $source === 'register'
            ? 'register'
            : 'login';

        return redirect()
            ->route($route)
            ->withErrors([
                'social' => $message,
            ]);
    }

    private function validateProvider(
        string $provider,
    ): string {
        abort_unless(
            in_array(
                $provider,
                self::PROVIDERS,
                true,
            ),
            404,
        );

        return $provider;
    }

    private function providerLabel(
        string $provider,
    ): string {
        return match ($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            default => ucfirst($provider),
        };
    }
}
