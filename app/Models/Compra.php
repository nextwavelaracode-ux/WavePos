<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = [
        'sucursal_id',
        'proveedor_id',
        'user_id',
        'numero_factura',
        'fecha_compra',
        'tipo_compra',
        'metodo_pago',
        'fecha_vencimiento',
        'subtotal',
        'total_impuestos',
        'total_descuentos',
        'total',
        'saldo_pendiente',
        'total_pagado',
        'estado',
        'estado_pago',
        'observaciones',
    ];

    protected $casts = [
        'fecha_compra'      => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal'          => 'decimal:2',
        'total_impuestos'   => 'decimal:2',
        'total_descuentos'  => 'decimal:2',
        'total'             => 'decimal:2',
        'saldo_pendiente'   => 'decimal:2',
        'total_pagado'      => 'decimal:2',
    ];

    // ── Relaciones ─────────────────────────────────

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoCompra::class, 'compra_id')->orderBy('fecha_pago', 'desc');
    }

    // ── Helpers / Accessors ────────────────────────

    public function getEstadoBadgeColorAttribute(): string
    {
        return match ($this->estado) {
            'registrada' => 'emerald',
            'anulada'    => 'red',
            'devuelta'   => 'orange',
            default      => 'gray',
        };
    }

    public function getEstadoPagoBadgeColorAttribute(): string
    {
        return match ($this->estado_pago) {
            'pagado'    => 'emerald',
            'pendiente' => 'orange',
            'parcial'   => 'blue',
            'vencido'   => 'red',
            default     => 'gray',
        };
    }

    public function getTipoCompraBadgeColorAttribute(): string
    {
        return match ($this->tipo_compra) {
            'contado' => 'blue',
            'credito' => 'purple',
            default   => 'gray',
        };
    }
}
