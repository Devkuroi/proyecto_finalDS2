<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Rol;
use App\Models\PuntoAtencion;

class AuthController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión con selector rápido Demo Mode
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Cargar usuarios de prueba para el selector rápido de demostración
        $usuariosDemo = User::with(['rol', 'puntoAtencion'])
            ->where('activo', 1)
            ->get()
            ->map(function ($u) {
                return [
                    'email' => $u->email,
                    'rol' => $u->rol->nombre,
                    'nombre' => $u->nombre,
                    'sede' => $u->puntoAtencion ? $u->puntoAtencion->nombre : 'Oficina Central',
                    'comuna' => $u->puntoAtencion ? $u->puntoAtencion->comuna_numero : null,
                ];
            });

        return view('auth.login', compact('usuariosDemo'));
    }

    /**
     * Autentica al usuario y redirige según su perfil institucional (Req 1)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese una dirección de correo válida.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'activo' => 1], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user()->load(['rol', 'puntoAtencion']);
            $rolNombre = strtoupper($user->rol->nombre);

            // Determinar la ruta de redirección según el rol institucional
            $redirectUrl = match ($rolNombre) {
                'VENTANILLA' => route('ventanilla.index'),
                'ENRUTADOR' => route('enrutador.bitacora'),
                'ADMINISTRADOR' => route('dashboard', ['rol' => 'ADMINISTRADOR']),
                'ENCUESTADOR' => route('dashboard', ['rol' => 'ENCUESTADOR']),
                'CALIDAD' => route('dashboard', ['rol' => 'CALIDAD']),
                default => route('dashboard'),
            };

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => "Bienvenido(a), {$user->nombre}. Acceso concedido con perfil {$rolNombre}.",
                    'redirect' => $redirectUrl,
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'rol' => $rolNombre,
                        'punto_atencion' => $user->puntoAtencion ? [
                            'id' => $user->puntoAtencion->id,
                            'nombre' => $user->puntoAtencion->nombre,
                            'comuna_numero' => $user->puntoAtencion->comuna_numero,
                            'es_principal' => $user->puntoAtencion->es_principal,
                        ] : null,
                    ],
                ]);
            }

            return redirect()->intended($redirectUrl);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas o usuario inactivo en el sistema.',
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros o el usuario está inactivo.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión activa
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Sesión finalizada exitosamente.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('info', 'Sesión cerrada correctamente.');
    }

    /**
     * Endpoint API para consultar el usuario en sesión activa
     */
    public function me(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'user' => null,
            ], 401);
        }

        $user = Auth::user()->load(['rol', 'puntoAtencion']);

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'identificacion' => $user->identificacion,
                'rol' => $user->rol->nombre,
                'punto_atencion' => $user->puntoAtencion,
            ],
        ]);
    }
}
