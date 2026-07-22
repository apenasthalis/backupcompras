<?php

use App\Exceptions\ApiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt' => \App\Http\Middleware\JwtMiddleware::class,
        ]);

        $middleware->encryptCookies(except: [
            'jwt_token',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (ApiException $e) {
            $response = ['message' => $e->getMessage()];
            if ($e->getErrors() !== null) {
                $response['errors'] = $e->getErrors();
            }
            return response()->json($response, $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e) {
            if (request()->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                ], $status >= 100 && $status < 600 ? $status : 500);
            }
        });
    })->create();
