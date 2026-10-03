<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Wnikk\LaravelAccessRules\Facades\Access;

/**
 * Turns on the debug mode of access rules for one request.
 *
 * In a real application the condition is a support session: an administrator looks at the
 * application as the user who complains and has ticked "debug". An explanation shows rules of
 * other owners, their conditions and values of attributes, so ordinary users never get the mode.
 * The sandbox accepts a query parameter, and only in the local environment.
 */
class AccessDebug
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local', 'testing') && $request->boolean('access_debug')) {
            Access::debug();
        }

        return $next($request);
    }
}
