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
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    private const PROVIDERS = [
        'google',
        'facebook',
    ];

    private const INTENT_AUTHENTICATE = 'authenticate';

    private const INTENT_LINK = 'link';

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
            'social-auth.intent',
            self::INTENT_AUTHENTICATE,
        );

        $request->session()->put(
            'social-auth.source',
            $source,
        );

        $request->session()->forget(
            'social-auth.link_user_id',
        );

        return Socialite::driver(
            $provider,
        )->redirect();
    }

    public function linkRedirect(
        Request $request,
        string $provider,
    ): SymfonyRedirectResponse {
        $provider = $this->validateProvider(
            $provider,
        );

        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $request->session()->put(
            'social-auth.intent',
            self::INTENT_LINK,
        );

        $request->session()->put(
            'social-auth.link_user_id',
            $user->id,
        );

        $request->session()->forget(
            'social-auth.source',
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

        $intent = $request->session()->get(
            'social-auth.intent',
            self::INTENT_AUTHENTICATE,
        );

        if ($intent === self::INTENT_LINK) {
            return $this->completeLink(
                $request,
                $provider,
                $providerUserId,
            );
        }

        if ($request->user() instanceof User) {
            $this->clearSocialAuthState(
                $request,
            );

            return to_route(
                'profile.edit',
            )->withErrors([
                'social' => 'Sesi autentikasi sosial tidak valid untuk akun yang sedang masuk.',
            ]);
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

            $this->clearSocialAuthState(
                $request,
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

        $this->clearSocialAuthState(
            $request,
        );

        return redirect()->intended(
            route('dashboard'),
        );
    }

    private function completeLink(
        Request $request,
        string $provider,
        string $providerUserId,
    ): RedirectResponse {
        $user = $request->user();

        $linkUserId = $request->session()->get(
            'social-auth.link_user_id',
        );

        if (
            ! $user instanceof User
            || ! is_numeric($linkUserId)
            || (int) $linkUserId !== (int) $user->getKey()
        ) {
            $this->clearSocialAuthState(
                $request,
            );

            return to_route(
                'login',
            )->withErrors([
                'social' => 'Sesi penautan akun tidak valid atau sudah berakhir. Silakan masuk kembali.',
            ]);
        }

        try {
            $result = DB::transaction(
                function () use (
                    $user,
                    $provider,
                    $providerUserId,
                ): string {
                    $providerAccount = SocialAccount::query()
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
                        $providerAccount !== null
                        && (int) $providerAccount->user_id !== (int) $user->id
                    ) {
                        return 'owned_by_other_user';
                    }

                    $currentAccount = SocialAccount::query()
                        ->where(
                            'user_id',
                            $user->id,
                        )
                        ->where(
                            'provider',
                            $provider,
                        )
                        ->lockForUpdate()
                        ->first();

                    if (
                        $currentAccount !== null
                        && (string) $currentAccount->provider_user_id !== $providerUserId
                    ) {
                        return 'already_linked_to_different_account';
                    }

                    if ($currentAccount === null) {
                        SocialAccount::query()->create([
                            'user_id' => $user->id,
                            'provider' => $provider,
                            'provider_user_id' => $providerUserId,
                        ]);
                    }

                    return 'linked';
                },
            );
        } catch (Throwable $exception) {
            report($exception);

            $this->clearSocialAuthState(
                $request,
            );

            return to_route(
                'profile.edit',
            )->withErrors([
                'social' => 'Akun '
                    .$this->providerLabel($provider)
                    .' gagal ditautkan. Silakan coba lagi.',
            ]);
        }

        $this->clearSocialAuthState(
            $request,
        );

        if ($result === 'owned_by_other_user') {
            return to_route(
                'profile.edit',
            )->withErrors([
                'social' => 'Akun '
                    .$this->providerLabel($provider)
                    .' tersebut sudah terhubung ke akun SkillPath AI lain.',
            ]);
        }

        if ($result === 'already_linked_to_different_account') {
            return to_route(
                'profile.edit',
            )->withErrors([
                'social' => 'Akun SkillPath AI ini sudah memiliki akun '
                    .$this->providerLabel($provider)
                    .' yang terhubung.',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Akun '
                .$this->providerLabel($provider)
                .' berhasil ditautkan.',
        ]);

        return to_route(
            'profile.edit',
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
        $intent = $request
            ->session()
            ->get(
                'social-auth.intent',
                self::INTENT_AUTHENTICATE,
            );

        $source = $request
            ->session()
            ->get(
                'social-auth.source',
                'login',
            );

        $user = $request->user();

        $this->clearSocialAuthState(
            $request,
        );

        if (
            $intent === self::INTENT_LINK
            && $user instanceof User
        ) {
            return to_route(
                'profile.edit',
            )->withErrors([
                'social' => $message,
            ]);
        }

        $route = $source === 'register'
            ? 'register'
            : 'login';

        return redirect()
            ->route($route)
            ->withErrors([
                'social' => $message,
            ]);
    }

    private function clearSocialAuthState(
        Request $request,
    ): void {
        $request->session()->forget([
            'social-auth.intent',
            'social-auth.source',
            'social-auth.link_user_id',
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
