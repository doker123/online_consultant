<?php

namespace Middleware;

use Src\Auth\Auth;
use Src\Request;

class RoleMiddleware
{
    public function handle(Request $request, ?string $roles = null): ?Request
    {
        if (!Auth::check()) {
            app()->route->redirect('/login');
            exit();
        }

        if ($roles) {
            $allowedRoles = explode(',', $roles);
            $userRole = Auth::getUserType();
            if (!in_array($userRole, $allowedRoles, true)) {
                app()->route->redirect('/');
                exit();
            }
        }

        return $request;
    }
}
