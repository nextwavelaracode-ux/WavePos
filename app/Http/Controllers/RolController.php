<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(25);
        $permisos = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'general';
        });

        return view('pages.configuracion.roles', compact('roles', 'permisos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $rol = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $rol->syncPermissions($validated['permissions']);
        }

        return redirect()->route('configuracion.roles')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Creado!',
                'message' => 'El rol fue registrado correctamente.',
            ]);
    }

    public function update(Request $request, Role $rol)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:roles,name,' . $rol->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $rol->update(['name' => $validated['name']]);
        $rol->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('configuracion.roles')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizado!',
                'message' => 'El rol fue actualizado correctamente.',
            ]);
    }

    public function destroy(Role $rol)
    {
        $rol->delete();

        return redirect()->route('configuracion.roles')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminado!',
                'message' => 'El rol fue eliminado correctamente.',
            ]);
    }
}
