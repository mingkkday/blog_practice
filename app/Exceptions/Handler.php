<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use BadMethodCallException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {


        $this->reportable(function (RouteNotFoundException $e) {
            $this->message($e->getMessage());
        });
        $this->reportable(function (BadMethodCallException $e) {
            $this->message($e->getMessage());
        });
        $this->reportable(function (Exception $e) {
            $this->message($e->getMessage());
        });
        $this->reportable(function (Throwable $e) {
            $this->message($e->getMessage());
        });
    }

    public function message(string $message)
    {
        $result = [['exception' => $message], 500];
        return response()->json($message);
    }
}
