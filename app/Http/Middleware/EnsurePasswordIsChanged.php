<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user !== null && $user->must_change_password && ! $request->routeIs('password.initial.edit', 'password.initial.update', 'logout')) {
            return redirect()->route('password.initial.edit');
        }

        return $next($request);
    }
}
