<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class isUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::User()->role != "USER") {
            return response()->json(['statusCode' => 401, 'success' => false, 'message' => "unauthorized"], 401);
        }
        return $next($request);
    }
}