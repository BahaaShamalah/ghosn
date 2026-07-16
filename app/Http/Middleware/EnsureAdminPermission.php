<?php

namespace App\Http\Middleware;

use App\Support\AdminPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if (! $user->role_id) {
            if (app()->isProduction()) {
                abort(403, __('admin.errors.forbidden'));
            }

            \Database\Seeders\RolePermissionSeeder::assignSuperAdmin($user);
            $user->refresh();
        }

        $permission = AdminPermission::routePermission();

        if ($permission && ! $user->hasPermission($permission)) {
            abort(403, __('admin.errors.forbidden'));
        }

        return $next($request);
    }
}
