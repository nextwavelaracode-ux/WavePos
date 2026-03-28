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

        // Si falla, revisamos exactamente por qué para depuración
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'DEBUG: USUARIO NO ENCONTRADO EN LA BD',
            ])->onlyInput('email');
        }
        
        if (!\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'DEBUG: CONTRASEÑA INCORRECTA (HASH NO COINCIDE)',
            ])->onlyInput('email');
        } 
        
        if (!$user->estado) {
            return back()->withErrors([
                'email' => 'DEBUG: USUARIO INACTIVO ESTADO FALSE',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'DEBUG: EL USUARIO Y LA CLAVE ESTAN BIEN PERO AUTH::ATTEMPT FALLÓ POR OTRA RAZON',
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
