<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class AddAuthHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->bearerToken()) {
            if ($token = $request->cookie('_token')) {
              $request->headers->add(['Authorization' => 'Bearer '.$token]);

            }
        }

        return $next($request);
    }
}