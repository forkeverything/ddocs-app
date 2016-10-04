<?php

namespace App\Exceptions\Handlers;

use Illuminate\Support\Facades\Event;

trait JWTHandlers
{
    protected function handleTokenExpiredException(\Exception $exception)
    {
        return response()->json(['AUTH_TOKEN_EXPIRED'], $exception->getStatusCode());
    }

    protected function handleTokenInvalidException(\Exception $exception)
    {
        return response()->json(['AUTH_TOKEN_INVALID'], $exception->getStatusCode());
    }
}