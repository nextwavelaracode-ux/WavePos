<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuentaCobrar extends Model
{
    protected $table = 'pagos_cuentas_cobrar';

    protected $fillable = [
        'cuenta_id',
        'caja_id',
        'user_id',
        'monto',
        'metodo',
        'referencia',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getMetodoLabelAttribute(): string
    {
        return match($this->metodo) {
            'efectivo'      => 'Efectivo',
            'tarjeta'       => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'yappy'         => 'Yappy / Nequi',
            default         => ucfirst($this->metodo),
        };
    }

    public function getMetodoBadgeColorAttribute(): string
    {
        return match($this->metodo) {
            'efectivo'      => 'emerald',
            'tarjeta'       => 'blue',
            'transferencia' => 'purple',
            'yappy'         => 'amber',
            default         => 'gray',
        };
    }
}
