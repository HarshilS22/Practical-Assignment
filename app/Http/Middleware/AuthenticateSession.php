<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('user_id')) {
            return redirect()->route('login')
                ->with('error', 'Please login first');
        }

        return $next($request);
    }
}
