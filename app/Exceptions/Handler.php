<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Exception $exception)
    {
        // Handle 403 Forbidden errors (session timeout)
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        // Handle CSRF token mismatch
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('login')->with('error', 'Your session expired due to inactivity. Please log in again.');
        }

        // Handle authentication exceptions
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return redirect()->guest(route('login'))
            ->with('error', 'Please log in to access this page.');
    }
}