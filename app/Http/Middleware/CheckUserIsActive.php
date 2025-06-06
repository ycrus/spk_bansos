<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && ! Auth::user()->status) {
            Auth::logout();

            return redirect()->route('filament.admin.auth.login')->withErrors([
                'email' => 'Email is not active.',
            ]);
        }

        return $next($request);
    }
}
