<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        InvalidRequestException::class,
    ];
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception|Throwable $e
     * @return JsonResponse
     */
    public function render($request, Exception|Throwable $e): JsonResponse
    {
        Log::error($e->getMessage(), ['exception' => $e]);

        if ($e instanceof PetApiException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 502);
        }

        if ($e instanceof InvalidRequestException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        if ($e instanceof HttpException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json([
            'error' => 'An unexpected error occurred. Please try again later.',
        ], 500);
    }

    /**
     * Report or log an exception.
     *
     * @param Exception|Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Exception|Throwable $e): void
    {
        parent::report($e);
    }
}
