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

            $localUser = User::firstOrCreate(
                ['keycloak_id' => $tokenData->sub],
                [
                    'email' => $tokenData->email,
                ]
            );

            $rolesToSync = $this->filterRoles($incomingRoles);

            if (
                !$this->compareRoleSets(
                    $localUser->getRoleNames()->toArray(),
                    $rolesToSync
                )
            ) {
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

    private function filterRoles(array $incomingRoles): array
    {
        return array_values(array_filter($incomingRoles, function ($role) {
            return str_starts_with($role, "BOOK_");
        }));
    }

    private function compareRoleSets(array $currentRoles, array $rolesToSync): bool
    {
        sort($currentRoles);
        sort($rolesToSync);

        return $currentRoles === $rolesToSync;
    }
}
