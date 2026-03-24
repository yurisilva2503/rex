<?php

namespace App\Http\Middleware;

use App\Models\Indicator;
use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IndicatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificando se o usuário pode visualizar outros usuários
        if (!Auth::user()->can('viewAny', [Auth::user(), Indicator::class])) {
            abort(403);
        }
        return $next($request);
    }
}
