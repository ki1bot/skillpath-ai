<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClearAbandonedAssessmentSession
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $user = $request->user();

        if (
            ! $user
            || $request->routeIs(
                'assessment.*',
                'session.heartbeat',
            )
        ) {
            return $next(
                $request,
            );
        }

        $suffix = '.'.$user->getAuthIdentifier();

        foreach (
            array_keys(
                $request
                    ->session()
                    ->all(),
            ) as $key
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
            ) {
                continue;
            }

            $reserveKey = str_replace(
                'assessment.question_ids.',
                'assessment.reserve_question_ids.',
                $key,
            );

            $request
                ->session()
                ->forget([
                    $key,
                    $reserveKey,
                ]);
        }

        return $next(
            $request,
        );
    }
}
