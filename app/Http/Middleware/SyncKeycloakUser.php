<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use Spatie\Permission\Models\Role;

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
            $incomingRoles = $realmAccess->roles ?? [];

            $rolesToSync = array_values(array_filter($incomingRoles, function ($role) {
                return str_starts_with($role, "BOOK_");
            }));

            $localUser = User::firstOrCreate(
                ['keycloak_id' => $tokenData->sub],
                [
                    'email' => $tokenData->email,
                ]
            );
            $currentRoleNames = $localUser->getRoleNames()->toArray();
            sort($currentRoleNames);
            $rolesToSyncSorted = $rolesToSync;
            sort($rolesToSyncSorted);

            if ($currentRoleNames !== $rolesToSyncSorted) {
                foreach ($rolesToSync as $roleName) {
                    Role::firstOrCreate([
                        'name' => $roleName,
                        'guard_name' => 'api'
                    ]);
                }
                $localUser->syncRoles($rolesToSync);
            }

            Auth::setUser($localUser);
        }

        return $next($request);
    }
}
