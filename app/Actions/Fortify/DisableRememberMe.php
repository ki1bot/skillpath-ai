<?php

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Http\Request;

class DisableRememberMe
{
    public function __invoke(
        Request $request,
        Closure $next,
    ): mixed {
        $request->merge([
            'remember' => false,
        ]);

        return $next($request);
    }
}
