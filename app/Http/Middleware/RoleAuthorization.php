<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAuthorization
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $convertedRole = UserRole::from($role);

        if (!$request->user() || !$request->user()->user_role?->isRole($convertedRole)) {
            return redirect()->back(fallback: route('welcome'))->with('error', __('messages.http.forbidden'));
        }

        return $next($request);
    }
}
