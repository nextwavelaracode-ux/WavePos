<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';

    protected $fillable = [
        'venta_id',
        'cliente_id',
        'sucursal_id',
        'total',
        'total_pagado',
        'saldo_pendiente',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoCuentaCobrar::class, 'cuenta_id');
    }

    public function getEstadoBadgeColorAttribute(): string
    {
        return match($this->estado) {
            'pagado'    => 'emerald',
            'parcial'   => 'blue',
            'pendiente' => 'amber',
            'vencido'   => 'red',
            default     => 'gray',
        };
    }
}
