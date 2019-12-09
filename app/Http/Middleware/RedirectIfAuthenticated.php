<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            return $next($request);
        }

        $redirectUrl = $request->input('redirect');

        if (empty($redirectUrl)) {
            return redirect('authenticated');
        }
        $token = auth()->getToken();
        $url = $redirectUrl . '?token=' . $token;

        return redirect()->away($url);
    }
}
