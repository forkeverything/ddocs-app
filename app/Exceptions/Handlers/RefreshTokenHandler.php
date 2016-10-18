<?php


namespace App\Exceptions\Handlers;

trait RefreshTokenHandler
{
    protected function handleRefreshTokenInvalid()
    {
        return response()->json([
            'error' => 'refresh_token_invalid'
        ], 401);
    }

    protected function handleRefreshTokenRevoked()
    {
        return response()->json([
            'error' => 'refresh_token_revoked'
        ], 401);
    }

    protected function handleRefreshTokenExpired()
    {
        return response()->json([
            'error' => 'refresh_token_expired'
        ], 401);
    }
}