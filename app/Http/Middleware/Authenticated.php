<?php

namespace App\Http\Middleware;


use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class Authenticated {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

            if (! $user = auth()->checkOrFail()) {
                return response()->json([
                    'message' =>  __('auth.user-error')
                ], 404);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => __('auth.token-expired')
            ], 403);
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'message' => __('auth.token-invalid')
            ], 403);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => __('auth.token-error')
            ], 400);
        } catch (JWTException $e) {
            return response()->json([
                'message' => __('auth.token-absent')
            ], 400);
        }

        return $next($request);
    }

}