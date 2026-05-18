<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireProducer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = session('user');

        if (!$user || !in_array($user['role'], ['producer', 'admin'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Producer role required.'], 403);
            }
            return redirect('/')->with('error', 'Access denied. Producer privileges required.');
        }

        return $next($request);
    }
}
