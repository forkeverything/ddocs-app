<?php


namespace App\Exceptions\Handlers;


trait JWTHandler
{
    protected function handleTokenInvalidException($request, \Exception $exception)
    {
        return response()->json([
            'error' => 'token_invalid'
        ], 401);
    }

    protected function handleTokenExpiredException($request, \Exception $exception)
    {
        return response()->json([
            'error' => 'token_expired'
        ], 401);
    }

    protected function handleUnauthorizedHttpException($request, \Exception $exception)
    {
        return response()->json([
            'error' => 'token_revoked'
        ], 401);
    }
}