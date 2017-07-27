<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Core\Exceptions\MailerException;
use Core\Exceptions\UnauthorizedException;
use Core\Exceptions\AuthenticationException;
use Core\Exceptions\ResourceNotFoundException;
use Core\Exceptions\ValidationException as CoreValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        AuthenticationException::class,
        CoreValidationException::class,
        ResourceNotFoundException::class,
    ];

    protected $internalHandlers = [
        AuthenticationException::class => 'handleAuthenticationException',
        UnauthorizedException::class => 'handleUnauthorizedException',
        CoreValidationException::class => 'handleCoreValidationException',
        MailerException::class => 'handleMailerException',
        ResourceNotFoundException::class => 'handleResourceNotFoundException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (array_key_exists(get_class($e), $this->internalHandlers)) {
            return call_user_func_array([$this, $this->internalHandlers[get_class($e)]], [$e]);
        }

        return parent::render($request, $e);
    }

    protected function handleAuthenticationException(AuthenticationException $e): Response
    {
        return response([
            'message' => $e->getMessage(),
        ], $e->getCode());
    }

    protected function handleUnauthorizedException(UnauthorizedException $e): Response
    {
        return response([
            'message' => $e->getMessage(),
        ], $e->getCode());
    }

    protected function handleCoreValidationException(CoreValidationException $e): Response
    {
        return response([
            'message' => $e->getMessage(),
            'errors' => $e->errors,
        ], $e->getCode());
    }

    protected function handleMailerException(MailerException $e): Response
    {
        return response([
            'message' => $e->getMessage(),
            'errors' => $e->errors,
        ], $e->getCode());
    }

    protected function handleResourceNotFoundException(ResourceNotFoundException $e): Response
    {
        return response([
            'message' => $e->getMessage(),
        ], $e->getCode());
    }
}
