<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSuspension
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->user_role?->isRole(UserRole::SUSPENDED)) {
            return redirect()->route('suspended');
        }

        return $next($request);
    }
}
