<?php

namespace App\Exceptions;

use App\Coupon;
use App\Events\UserHasRunOutOfCredits;
use App\Exceptions\Handlers\AuthenticationHandler;
use App\Exceptions\Handlers\AuthorizationHandler;
use App\Exceptions\Handlers\CouponHandlers;
use App\Exceptions\Handlers\JWTHandler;
use App\Exceptions\Handlers\RefreshTokenHandler;
use App\Exceptions\Handlers\UserHandlers;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Event;

class Handler extends ExceptionHandler
{
    // Traits that hold our handle methods.
    use CouponHandlers, UserHandlers, AuthenticationHandler, JWTHandler, RefreshTokenHandler, AuthorizationHandler;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Look for a handle method that matches the exception and call it.
        $reflector = new \ReflectionClass($exception);
        $methodName = 'handle' . $reflector->getShortName();
        if(in_array($methodName, get_class_methods(Handler::class))) return $this->{$methodName}($request, $exception);
        
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
