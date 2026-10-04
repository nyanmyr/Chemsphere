<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() &&
        !$request->user()->user_role?->isRole(UserRole::PENDING) &&
        !$request->user()->user_role?->isRole(UserRole::SUSPENDED)) {
            return redirect()->route('welcome');
        }

        return $next($request);
    }
}
