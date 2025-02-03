<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }
    // }

    /**
     * Handle unauthenticated requests.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @return void
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Unauthorized',
            'status' => 'false',
            'error' => 'Unauthorized'
        ], 401));
    }
}
