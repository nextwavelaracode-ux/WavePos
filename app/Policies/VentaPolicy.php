<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;

class VentaPolicy
{
    /**
     * Determine whether the user can anular the model.
     */
    public function anular(User $user, Venta $venta): bool
    {
        // Si el usuario es SuperAdmin, siempre permitimos la acción
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // 1. Que no esté anulada ya
        if ($venta->estado === 'anulada') {
            return false;
        }

        // 2. Que pertenezca a la misma sucursal del usuario
        if ($user->sucursal_id !== $venta->sucursal_id) {
            return false;
        }

        // 3. Que sea del mismo día actual (turno vigente)
        // Usamos la fecha de la venta vs la fecha actual
        $hoy = Carbon::today()->toDateString();
        if ($venta->fecha !== $hoy) {
            return false;
        }

        return true;
    }
}
