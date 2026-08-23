<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthorization
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->user_role?->isAdmin()) {
            return redirect()->back(fallback: route('welcome'));
        }

        return $next($request);
    }
}
