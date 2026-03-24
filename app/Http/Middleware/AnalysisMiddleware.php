<?php

namespace App\Http\Middleware;

use App\Models\Analysis;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnalysisMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificando se o usuário pode visualizar análises
        if (!Auth::user()->can('viewAny', [Auth::user(), Analysis::class])) {
            abort(403);
        }
        return $next($request);
    }
}
