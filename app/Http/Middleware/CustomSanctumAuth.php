<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class CustomSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'message' => 'Token inválido ou sessão expirada. Por favor, faça login novamente.',
                'error' => 'Unauthenticated',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
