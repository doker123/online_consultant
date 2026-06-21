<?php

namespace Middleware;

use Src\Auth\Auth;
use Src\Request;

class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (!Auth::check()) {
            app()->route->redirect("/login");
            exit();
        }
    }
}
