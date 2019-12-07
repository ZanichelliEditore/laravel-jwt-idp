<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $mandatoryRole)
    {

        if (!Auth::guard()->check()) {
            return redirect('loginForm');
        }

        $user = auth()->user();

        foreach ($user->roles as $role) {
            if ($role->role->name === $mandatoryRole) {
                return $next($request);
            }
        }

        return redirect('authenticated');
    }

}