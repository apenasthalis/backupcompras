<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function __construct(
        private readonly JwtService $jwtService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Token not provided'], 401);
            }
            return redirect('/login');
        }

        $user = $this->jwtService->getUserFromToken($token);

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Invalid or expired token'], 401);
            }
            return redirect('/login');
        }

        auth()->setUser($user);

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        if ($request->hasCookie('jwt_token')) {
            return $request->cookie('jwt_token');
        }

        if ($request->has('token')) {
            return $request->query('token');
        }

        return null;
    }
}
