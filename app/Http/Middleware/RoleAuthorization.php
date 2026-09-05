<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAuthorization
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $convertedRole = UserRole::from($role);

        if (!$request->user() || !$request->user()->user_role?->isRole($convertedRole)) {
            return redirect()->back(fallback: route('welcome'))->withErrors(['role' => 'Invalid role to access this page.']);
        }

        return $next($request);
    }
}
