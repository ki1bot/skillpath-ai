<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class EmailVerificationController extends Controller
{
    private const CODE_TTL_MINUTES = 10;

    private const RESEND_COOLDOWN_SECONDS = 300;

    public function show(Request $request): Response|RedirectResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user->email_verified_at !== null) {
            return to_route('profile.edit');
        }

        return Inertia::render('settings/verify-email', [
            'email' => $user->email,
            'status' => $request->session()->get('status'),
            'resendAvailableIn' => $this->resendCooldownRemaining(
                $user->id,
            ),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user->email_verified_at !== null) {
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => 'Email Anda sudah terverifikasi.',
            ]);

            return to_route('profile.edit');
        }

        if ($this->resendCooldownRemaining($user->id) > 0) {
            return to_route('email-verification.show')
                ->with('status', 'verification-code-cooldown');
        }

        if (! $this->beginResendCooldown($user->id)) {
            return to_route('email-verification.show')
                ->with('status', 'verification-code-cooldown');
        }

        $code = (string) random_int(100000, 999999);

        try {
            Cache::put(
                $this->cacheKey($user->id),
                [
                    'email' => Str::lower((string) $user->email),
                    'code_hash' => $this->hashCode($code),
                ],
                now()->addMinutes(self::CODE_TTL_MINUTES),
            );

            Mail::to((string) $user->email)->send(
                new EmailVerificationCodeMail($code),
            );
        } catch (Throwable $exception) {
            Cache::forget($this->cacheKey($user->id));
            Cache::forget($this->resendCacheKey($user->id));

            report($exception);

            return to_route('email-verification.show')
                ->with('status', 'verification-code-failed');
        }

        return to_route('email-verification.show')
            ->with('status', 'verification-code-sent');
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $this->authenticatedUser($request);

        if ($user->email_verified_at !== null) {
            return to_route('profile.edit');
        }

        $payload = Cache::get($this->cacheKey($user->id));

        if (
            ! is_array($payload)
            || ! isset($payload['email'], $payload['code_hash'])
        ) {
            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi sudah kedaluwarsa. Kirim kode baru.',
            ]);
        }

        $storedEmail = Str::lower((string) $payload['email']);
        $currentEmail = Str::lower((string) $user->email);

        if (! hash_equals($storedEmail, $currentEmail)) {
            Cache::forget($this->cacheKey($user->id));
            Cache::forget($this->resendCacheKey($user->id));

            throw ValidationException::withMessages([
                'code' => 'Email akun berubah. Kirim kode verifikasi baru.',
            ]);
        }

        $submittedCodeHash = $this->hashCode(
            (string) $validated['code'],
        );

        if (
            ! hash_equals(
                (string) $payload['code_hash'],
                $submittedCodeHash,
            )
        ) {
            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi tidak valid.',
            ]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Cache::forget($this->cacheKey($user->id));
        Cache::forget($this->resendCacheKey($user->id));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Email berhasil diverifikasi.',
        ]);

        return to_route('profile.edit');
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        return $user;
    }

    private function cacheKey(int $userId): string
    {
        return 'email-verification:'.$userId;
    }

    private function resendCacheKey(int $userId): string
    {
        return 'email-verification-resend:'.$userId;
    }

    private function beginResendCooldown(int $userId): bool
    {
        $expiresAt = now()->addSeconds(
            self::RESEND_COOLDOWN_SECONDS,
        );

        return Cache::add(
            $this->resendCacheKey($userId),
            $expiresAt->getTimestamp(),
            $expiresAt,
        );
    }

    private function resendCooldownRemaining(int $userId): int
    {
        $cacheKey = $this->resendCacheKey($userId);
        $availableAt = Cache::get($cacheKey);

        if (
            ! is_int($availableAt)
            && ! is_numeric($availableAt)
        ) {
            return 0;
        }

        $remaining = (int) $availableAt - now()->getTimestamp();

        if ($remaining <= 0) {
            Cache::forget($cacheKey);

            return 0;
        }

        return $remaining;
    }

    private function hashCode(string $code): string
    {
        return hash_hmac(
            'sha256',
            $code,
            (string) config('app.key'),
        );
    }
}
