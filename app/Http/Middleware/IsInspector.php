<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsInspector
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()?->role !== 'inspector') {
            abort(403);
        }

        return $next($request);
    }
}