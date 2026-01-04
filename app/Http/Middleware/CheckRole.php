<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Verificar si el usuario tiene el rol requerido
        if (!$user->hasRole($role)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
