<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Ensure the authenticated user holds one of the given roles.
     *
     * Usage in routes: ->middleware('role:Admin,Faculty Staff')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $roleName = optional($user->loadMissing('role')->role)->role_name;

        // Keep the session role in sync with the persisted role so views and
        // controllers relying on session('user_role') stay authoritative.
        if ($roleName && session('user_role') !== $roleName) {
            session(['user_role' => $roleName]);
        }

        if (! in_array($roleName, $roles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
