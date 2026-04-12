<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Revisar que de forma asíncrona el usuario no haya sido apagado por un administrador 
        // mientras tenía la sesión activa.
        if (Auth::check() && !Auth::user()->estado) {
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Su cuenta ha sido deshabilitada por administración. Comuníquese con el soporte.',
            ]);
        }

        return $next($request);
    }
}
