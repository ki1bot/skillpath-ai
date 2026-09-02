<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $user = $request->user();

        if (
            ! $user instanceof User
            || $user->email_verified_at !== null
        ) {
            return $next($request);
        }

        if (
            $request->routeIs(
                'email-verification.*',
                'profile.*',
                'logout',
                'session.heartbeat',
                'home',
                'about',
                'careers.public',
                'careers.public.show',
            )
        ) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(
                [
                    'message' => 'Email harus diverifikasi sebelum menggunakan SkillPath AI.',
                ],
                403,
            );
        }

        return redirect()
            ->route('email-verification.show')
            ->with(
                'status',
                'verification-required',
            );
    }
}
