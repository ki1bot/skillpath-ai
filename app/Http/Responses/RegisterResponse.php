<?php

namespace App\Http\Responses;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Throwable;

class RegisterResponse implements RegisterResponseContract
{
    private const CODE_TTL_MINUTES = 10;

    private const RESEND_COOLDOWN_SECONDS = 300;

    public function toResponse($request): mixed
    {
        $user = Auth::user();

        $status = 'verification-required';

        if (
            $user instanceof User
            && $user->email_verified_at === null
        ) {
            $code = (string) random_int(
                100000,
                999999,
            );

            $cacheKey = 'email-verification:'.$user->id;
            $resendCacheKey = 'email-verification-resend:'.$user->id;

            Cache::put(
                $cacheKey,
                [
                    'email' => Str::lower(
                        (string) $user->email,
                    ),
                    'code_hash' => $this->hashCode(
                        $code,
                    ),
                ],
                now()->addMinutes(self::CODE_TTL_MINUTES),
            );

            try {
                Mail::to(
                    (string) $user->email,
                )->send(
                    new EmailVerificationCodeMail(
                        $code,
                    ),
                );

                $cooldownUntil = now()->addSeconds(
                    self::RESEND_COOLDOWN_SECONDS,
                );

                Cache::put(
                    $resendCacheKey,
                    $cooldownUntil->timestamp,
                    $cooldownUntil,
                );

                $status = 'verification-code-sent';
            } catch (Throwable $exception) {
                Cache::forget($cacheKey);
                Cache::forget($resendCacheKey);

                report($exception);

                $status = 'verification-code-failed';
            }
        }

        $message = 'Akun berhasil dibuat. Verifikasi alamat email sebelum menggunakan SkillPath AI.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'verification_required' => true,
            ], 201);
        }

        return redirect()
            ->route('email-verification.show')
            ->with(
                'status',
                $status,
            );
    }

    private function hashCode(
        string $code,
    ): string {
        return hash_hmac(
            'sha256',
            $code,
            (string) config('app.key'),
        );
    }
}
