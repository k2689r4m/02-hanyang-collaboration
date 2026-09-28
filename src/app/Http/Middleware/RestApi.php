<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestApi
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
        if (Auth::check()) {
            if (in_array(Auth::user()->authority, [1, 2, 3, 4])) {
                return $next($request);
            }

            Auth::logout();
        }

        return redirect()->route('/');
    }
}
