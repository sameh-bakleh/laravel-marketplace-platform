<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  string  $roles  Pipe-separated roles, e.g. "admin" or "admin|seller"
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $allowed = array_values(array_filter(array_map('trim', explode('|', $roles))));
        if ($allowed === []) {
            abort(500, 'Role middleware misconfigured.');
        }

        if (! in_array($user->role->value, $allowed, true)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
