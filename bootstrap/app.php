<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\LogRequest::class);
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'log.request' => \App\Http\Middleware\LogRequest::class,
            'api.cors' => \App\Http\Middleware\ApiCorsMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = 500;
                $message = $e->getMessage();
                $code = 'INTERNAL_ERROR';
                $errors = [];

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                    $message = 'The given data was invalid.';
                    $code = 'VALIDATION_ERROR';
                    $errors = $e->errors();
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException || $e instanceof \App\Exceptions\AuthenticationException) {
                    $statusCode = 401;
                    $message = 'Unauthenticated';
                    $code = 'AUTH_ERROR';
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException || $e instanceof \App\Exceptions\AuthorizationException) {
                    $statusCode = 403;
                    $message = 'This action is unauthorized.';
                    $code = 'FORBIDDEN';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $statusCode = 404;
                    $message = 'Resource not found';
                    $code = 'NOT_FOUND';
                } elseif ($e instanceof \App\Exceptions\ApiException) {
                    $statusCode = $e->getStatusCode();
                    $code = 'API_ERROR';
                } elseif ($e instanceof \App\Exceptions\DatabaseException) {
                    $statusCode = $e->getStatusCode();
                    $code = 'DATABASE_ERROR';
                } elseif ($e instanceof \Illuminate\Database\QueryException) {
                    $statusCode = 500;
                    $message = config('app.debug') ? $e->getMessage() : 'Database error occurred';
                    $code = 'DATABASE_ERROR';
                } elseif ($e instanceof \App\Exceptions\ValidationException) {
                    $statusCode = $e->getStatusCode();
                    $code = 'VALIDATION_ERROR';
                    $errors = $e->getErrors();
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $statusCode = $e->getStatusCode();
                    $message = $e->getMessage();
                    $code = 'HTTP_ERROR';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'code' => $code,
                    'errors' => (object) $errors,
                ], $statusCode);
            }
        });
    })->create();
