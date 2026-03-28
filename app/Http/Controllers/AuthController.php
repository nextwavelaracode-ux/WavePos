<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar vista de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.auth.signin', ['title' => 'Sign In']);
    }

    /**
     * Procesar login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Verificamos si existe el usuario y si está activo
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'estado' => true])) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Bienvenido al sistema.');
        }

        // Si falla, revisamos si es por estado
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && !\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->onlyInput('email');
        } else if ($user && !$user->estado) {
            return back()->withErrors([
                'email' => 'Su cuenta está inactiva. Comuníquese con el administrador.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Procesar logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin')->with('success', 'Sesión cerrada correctamente.');
    }
}
