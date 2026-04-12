<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios  = User::with(['roles', 'sucursal'])->latest()->paginate(25);
        $roles     = Role::orderBy('name')->get();
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();

        return view('pages.configuracion.usuarios', compact('usuarios', 'roles', 'sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'apellido'    => 'nullable|string|max:100',
            'email'       => 'required|email|unique:users,email|max:150',
            'telefono'    => 'nullable|string|max:20',
            'password'    => 'required|string|min:6|confirmed',
            'rol'         => 'required|string|exists:roles,name',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'estado'      => 'nullable|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['estado'] = $request->boolean('estado', true);

        $rol = $validated['rol'];
        unset($validated['rol']);

        $usuario = User::create($validated);
        $usuario->syncRoles([$rol]);

        return redirect()->route('configuracion.usuarios')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Creado!',
                'message' => 'El usuario fue registrado correctamente.',
            ]);
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'apellido'    => 'nullable|string|max:100',
            'email'       => 'required|email|max:150|unique:users,email,' . $usuario->id,
            'telefono'    => 'nullable|string|max:20',
            'password'    => 'nullable|string|min:6|confirmed',
            'rol'         => 'required|string|exists:roles,name',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'estado'      => 'nullable|boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['estado'] = $request->boolean('estado', true);

        $rol = $validated['rol'];
        unset($validated['rol']);

        $usuario->update($validated);
        $usuario->syncRoles([$rol]);

        return redirect()->route('configuracion.usuarios')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizado!',
                'message' => 'El usuario fue actualizado correctamente.',
            ]);
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('configuracion.usuarios')
                ->with('sweet_alert', [
                    'type'    => 'error',
                    'title'   => 'Error',
                    'message' => 'No puedes eliminar tu propio usuario.',
                ]);
        }

        $usuario->delete();

        return redirect()->route('configuracion.usuarios')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminado!',
                'message' => 'El usuario fue eliminado correctamente.',
            ]);
    }
}
