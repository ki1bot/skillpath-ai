<?php

namespace App\Http\Middleware;

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

        $timeoutMinutes = (int) config(
            'security.idle_timeout_minutes',
            0,
        );

        if ($timeoutMinutes <= 0) {
            $request
                ->session()
                ->forget('auth.last_activity');

            $response = $next($request);

            if ($request->hasCookie(self::ACTIVITY_COOKIE)) {
                return $response->withCookie(
                    Cookie::forget(
                        self::ACTIVITY_COOKIE,
                    ),
                );
            }

            return $response;
        }

        $timeoutSeconds = $timeoutMinutes * 60;
        $now = now()->getTimestamp();

        $sessionValue = $request
            ->session()
            ->get('auth.last_activity');

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

        $request
            ->session()
            ->put(
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

    private function logout(
        Request $request,
        int $timeoutMinutes,
    ): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with(
                'status',
                "Sesimu berakhir karena tidak ada aktivitas selama {$timeoutMinutes} menit.",
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
