<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShareUserRole
{
    /**
     * Keep session('user_role') in sync with the authenticated user's
     * persisted role on every request. Login sets this value, but a restored
     * ("remember me") session would otherwise have it empty, breaking the
     * role-based UI and filters. This guarantees it is always correct.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            $roleName = optional($user->loadMissing('role')->role)->role_name;

            if ($roleName && $request->session()->get('user_role') !== $roleName) {
                $request->session()->put([
                    'user_role' => $roleName,
                    'user_role_id' => $user->role_id,
                    'user_id' => $user->user_id,
                ]);
            }
        }

        return $next($request);
    }
}
