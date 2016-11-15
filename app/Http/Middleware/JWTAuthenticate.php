<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

class JWTAuthenticate extends Authenticate
{

    /**
     * Check if the csrf within our auth token is the same as
     * the one we got from the http-only header cookie.
     *
     * @param Request $request
     * @throws TokenInvalidException
     */
    protected function validateCSRF(Request $request)
    {
        $cookieCSRF = $request->cookie('ddocs_csrf');
        $headerCSRF = $this->auth->parseToken()->getPayload()->get('csrf');
        if($cookieCSRF !== $headerCSRF) throw new TokenInvalidException;
    }

    /**
     * Overwrite authenticate method to include csrf verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @return void
     */
    public function authenticate(Request $request)
    {
        $this->checkForToken($request);
        
        $this->validateCSRF($request);

        try {
            $this->auth->parseToken()->authenticate();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }
    }

}
