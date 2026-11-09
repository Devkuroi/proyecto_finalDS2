<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado. Debe iniciar sesión.',
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder al sistema.');
        }

        $user = Auth::user();
        $userRole = strtoupper($user->rol?->nombre ?? '');

        $allowedRoles = array_map('strtoupper', $roles);

        // Si el usuario no tiene ninguno de los roles permitidos
        if (!in_array($userRole, $allowedRoles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => "Acceso no autorizado para el perfil [{$userRole}]. Rol requerido: " . implode(', ', $allowedRoles),
                ], 403);
            }
            return redirect()->route('dashboard')->with('error', 'No tiene permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
