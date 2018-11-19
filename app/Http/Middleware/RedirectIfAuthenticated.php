<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

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
    public function handle($request, Closure $next, $guard = null){

        $token = $request->session()->get('token');

        if(!$token){
            return null;
        }

        JWTAuth::setToken($token);
        if(JWTAuth::check()){
            return redirect('loginForm');
        }

        return $next($request);
    }

}
