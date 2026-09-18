<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckExpiredRolesAndPermissions
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->user()->purgeExpiredRolesAndPermissions();
        }

        return $next($request);
    }
}