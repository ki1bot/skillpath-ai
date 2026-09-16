<?php

namespace App\Http\Middleware;

use App\Support\AcademicAssessmentCatalog;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response;

class EnforceIdleTimeout
{
    private const ACTIVITY_COOKIE = 'auth_last_activity';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        $timeoutMinutes = max(
            1,
            (int) config('security.idle_timeout_minutes', 10),
        );

        $timeoutSeconds = $timeoutMinutes * 60;
        $now = now()->getTimestamp();

        if (
            $request->routeIs('assessment.*')
            && $this->hasActiveAssessmentSession($request)
        ) {
            $request->session()->put(
                'auth.last_activity',
                $now,
            );

            $response = $next($request);

            return $response->withCookie(
                $this->activityCookie(
                    $now,
                    $timeoutMinutes,
                ),
            );
        }

        $sessionValue = $request->session()->get(
            'auth.last_activity',
        );

        $sessionActivity = is_numeric($sessionValue)
            ? (int) $sessionValue
            : 0;

        $cookieValue = $request->cookie(
            self::ACTIVITY_COOKIE,
            '0',
        );

        $cookieActivity = is_string($cookieValue)
            && is_numeric($cookieValue)
                ? (int) $cookieValue
                : 0;

        $lastActivity = max(
            $sessionActivity,
            $cookieActivity,
        );

        if (
            $lastActivity === 0
            && Auth::guard('web')->viaRemember()
        ) {
            return $this->logout(
                $request,
                $timeoutMinutes,
            );
        }

        if (
            $lastActivity > 0
            && ($now - $lastActivity) >= $timeoutSeconds
        ) {
            return $this->logout(
                $request,
                $timeoutMinutes,
            );
        }

        $request->session()->put(
            'auth.last_activity',
            $now,
        );

        $response = $next($request);

        return $response->withCookie(
            $this->activityCookie(
                $now,
                $timeoutMinutes,
            ),
        );
    }

    private function hasActiveAssessmentSession(
        Request $request,
    ): bool {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        $suffix = '.'.$user->getAuthIdentifier();

        foreach (
            $request
                ->session()
                ->all() as $key => $value
        ) {
            if (
                ! is_string($key)
                || ! str_starts_with(
                    $key,
                    'assessment.question_ids.',
                )
                || ! str_ends_with(
                    $key,
                    $suffix,
                )
                || ! is_array($value)
                || count($value) !== AcademicAssessmentCatalog::QUESTION_LIMIT
            ) {
                continue;
            }

            $normalized = array_map(
                fn ($questionId): int => (int) $questionId,
                $value,
            );

            if (
                count(
                    array_unique(
                        $normalized,
                    ),
                ) === AcademicAssessmentCatalog::QUESTION_LIMIT
            ) {
                return true;
            }
        }

        return false;
    }

    private function logout(
        Request $request,
        int $timeoutMinutes,
    ): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'status',
                "Sesimu berakhir karena tidak ada aktivitas selama {$timeoutMinutes} menit. Silakan masuk kembali.",
            )
            ->withCookie(
                Cookie::forget(
                    self::ACTIVITY_COOKIE,
                ),
            );
    }

    private function activityCookie(
        int $timestamp,
        int $timeoutMinutes,
    ): SymfonyCookie {
        return Cookie::make(
            self::ACTIVITY_COOKIE,
            (string) $timestamp,
            $timeoutMinutes,
            (string) config(
                'session.path',
                '/',
            ),
            config('session.domain'),
            (bool) config(
                'session.secure',
                false,
            ),
            true,
            false,
            config(
                'session.same_site',
                'lax',
            ),
        );
    }
}
