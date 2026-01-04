<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function actionLogin(Request $request)
    {
        // 1. Si ya está autenticado
        if (Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ya está autenticado',
                    'redirect' => $this->getRoleRedirectUrl()
                ]);
            }
            return $this->redirectByRole($request);
        }

        // 2. Procesar login (POST)
        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            if (!Auth::attempt($credentials)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Las credenciales ingresadas no son válidas.'
                    ], 401);
                }

                return back()
                    ->withErrors(['email' => 'Credenciales incorrectas'])
                    ->withInput();
            }

            // ✅ Verificar si el usuario tiene roles asignados
            $user = Auth::user();
            if ($user->roles()->count() === 0) {
                // Cerrar sesión si no tiene roles
                Auth::logout();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El usuario no tiene permisos para iniciar sesión. Contacte al administrador.'
                    ], 403);
                }

                return back()
                    ->withErrors(['email' => 'El usuario no tiene permisos para iniciar sesión. Contacte al administrador.'])
                    ->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            // ✅ Toast session
            session()->flash('show_login_toast', true);

            // ✅ Redirección por rol
            return $this->redirectByRole($request);
        }

        // 3. Mostrar vista login (GET)
        return view('login.login');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = null;

        if ($request->expectsJson()) {
            $response = response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente',
                'redirect' => url('/login')
            ]);
        } else {
            $response = redirect('/login');
        }

        // Headers anti-cache
        return $response->withHeaders([
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT'
        ]);
    }

    /**
     * Obtener URL de redirección según rol
     */
    private function getRoleRedirectUrl(): string
    {
        $user = Auth::user();

        // Docente
        if ($user->roles()->where('name', 'docente')->exists()) {
            return url('/docente');
        }

        // Admin (por defecto)
        return url('/admin');
    }

    /**
     * Redirecciona según rol
     */
    private function redirectByRole(Request $request)
    {
        $user = Auth::user();

        // Docente
        if ($user->roles()->where('name', 'docente')->exists()) {
            return $this->jsonOrRedirect($request, '/docente');
        }

        // Admin (por defecto)
        return $this->jsonOrRedirect($request, '/admin');
    }

    /**
     * Decide entre JSON o Redirect normal
     */
    private function jsonOrRedirect(Request $request, string $url)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'redirect' => url($url)
            ]);
        }

        return redirect()->intended($url);
    }
}
