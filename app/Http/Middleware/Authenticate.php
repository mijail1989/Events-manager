<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle(Request $request, Closure $next){
        $user =  auth('sanctum')->user();
        if($user){
            return $next($request);
        }
        return response([
                 'message' => 'Unauthenticated'
              ], 403);
    }
}
