<?php

namespace App\Exceptions\Handlers;

use Illuminate\Support\Facades\Event;

trait AuthenticationHandler
{
    protected function handleAuthenticationException($request, \Exception $exception)
    {
        return response()->json([
            'error' => 'unauthenticated'
        ], 401);
    }
}