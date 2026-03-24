<?php

namespace App\Http\Middleware;

use App\Models\ActionPlan;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionPlanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificando se o usuário pode visualizar planos de ação
        if (!Auth::user()->can('viewAny', [Auth::user(), ActionPlan::class])) {
            abort(403);
        }
        return $next($request);
    }
}
