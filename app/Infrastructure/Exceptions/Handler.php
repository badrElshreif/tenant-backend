<?php

namespace App\Infrastructure\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, string>
     */
    protected $dontReport = [
        // ... other exceptions
    ];

    /**
     * A list of the exception types that should be reported but not logged.
     *
     * @var array<int, string>
     */
    protected $dontReportWithoutDebug = [
        // ... other exceptions
    ];

    public function render($request, \Throwable $exception)
    {

        if ($exception instanceof ModelNotFoundException) {
            dd("fff");
            if($request->acceptsJson()){

            }
        }

        return parent::render($request, $exception);
    }
}
