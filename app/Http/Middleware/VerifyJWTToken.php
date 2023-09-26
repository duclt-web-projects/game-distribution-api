<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class VerifyJWTToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = $this->auth->parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 500);
            }
        } catch (JWTException $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['message' => 'Token is Invalid'], 401);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json(['message' => 'Token is Expired'], 401);
            } else {
                return response()->json(['message' => 'Authorization Token not found'], 401);
            }
        }
        return $next($request);
    }
}
