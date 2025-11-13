<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SyncKeycloakUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $tokenUser = Auth::user();
            $tokenData = $tokenUser->token;
            $realmAccess = $tokenData->realm_access ?? [];
            $roles = $realmAccess->roles ?? [];

            $userRole = 'BOOK_USER';
            if (in_array('BOOK_ADMIN', $roles)) {
                $userRole = 'BOOK_ADMIN';
            } elseif (in_array('BOOK_USER', $roles)) {
                $userRole = 'BOOK_USER';
            }

            $localUser = User::updateOrCreate(
                ['keycloak_id' => $tokenData->sub],
                [
                    'email' => $tokenData->email,
                    'role' => $userRole,
                    'keycloak_id' => $tokenData->sub
                ]
            );
            Auth::setUser($localUser);
        }

        return $next($request);
    }
}
