<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::user() || ! Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        return $next($request);
    }
}
