<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard()->check()) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        if (!Auth::guard()->user()->HasRole("BOOK_ADMIN")) {
            return response()->json(['message' => 'Forbidden: Requires admin privileges.'], 403);
        }

        return $next($request);
    }
}
