<?php

namespace App\Http\Middleware;

use App\Models\Indicator;
use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IndicatorListMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificando se o usuário pode visualizar departamentos
        if (!Auth::user()->can('viewAny', [Auth::user(), Indicator::class])) {
             return response()->json([
                'error' => 'Erro: Você não tem permissão.'
            ], 500);
        }
        return $next($request);
    }
}
