<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = session('user');

        if (!$user || $user['role'] !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
            }
            return redirect('/')->with('error', 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
