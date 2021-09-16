<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Automatically get the loginToken from Cookies of the request
     * */
    public function handle($request, Closure $next, ...$guards)
    {
        if($loginToken = $request->cookie('loginToken')){
            $request->headers->set('Authorization', 'Bearer ' . $loginToken);
        }
        return parent::handle($request, $next, ...$guards);
    }
}
