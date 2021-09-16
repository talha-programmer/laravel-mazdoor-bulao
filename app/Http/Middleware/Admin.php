<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $userType = UserType::fromValue($user->user_type);
        if ($userType->is(UserType::Admin)) {
            return $next($request);
        }else{
            return response(['error', 'You are not allowed to perform this task!'], Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }
}
