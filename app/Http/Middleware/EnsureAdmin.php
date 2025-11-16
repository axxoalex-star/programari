<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'isAdmin') || !$user->isAdmin()) {
            abort(403, 'Acces interzis');
        }
        return $next($request);
    }
}
