<?php

namespace App\Exceptions\Handlers;

use Illuminate\Support\Facades\Event;

trait JWTHandlers
{
    protected function handleTokenExpiredException(\Exception $exception)
    {
        return response()->json(['token_expired'], $exception->getStatusCode());
    }

    protected function handleTokenInvalidException(\Exception $exception)
    {
        return response()->json(['token_invalid'], $exception->getStatusCode());
    }
}