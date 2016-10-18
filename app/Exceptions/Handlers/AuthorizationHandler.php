<?php


namespace App\Exceptions\Handlers;


use Illuminate\Http\Request;

trait AuthorizationHandler
{

    /**
     * Failed authorization response thrown by authorization
     * from a Controller.
     *
     * @param Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleAuthorizationException(Request $request, \Exception $e)
    {
        return response()->json([
            'error' => 'Not authorized to do that.'
        ], 403);
    }

}