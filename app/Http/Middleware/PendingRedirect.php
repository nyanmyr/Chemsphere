<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PendingRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->user_role?->isRole(UserRole::PENDING)) {
            return redirect()->route('pending');
        }

        return $next($request);
    }
}
