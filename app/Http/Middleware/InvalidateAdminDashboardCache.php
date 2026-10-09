<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\AdminWorkspaceController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class InvalidateAdminDashboardCache
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->isMethod('GET')
            || $request->isMethod('HEAD')
            || $request->isMethod('OPTIONS')
        ) {
            return $next($request);
        }

        try {
            return $next($request);
        } finally {
            Cache::forget(AdminWorkspaceController::CACHE_KEY);
        }
    }
}
