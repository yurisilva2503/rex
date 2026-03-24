<?php

namespace App\Http\Middleware;

use App\Models\Department;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       // Verificando se o usuário pode visualizar departamentos
        if (!Auth::user()->can('viewAny', [Auth::user(), Department::class])) {
            abort(403);
        }

        return $next($request);
    }
}
