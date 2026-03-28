<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoVenta extends Model
{
    protected $table = 'pagos_venta';

    protected $fillable = [
        'venta_id',
        'metodo',
        'monto',
        'referencia',
        'tipo_tarjeta',
        'banco',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function getMetodoLabelAttribute(): string
    {
        return match($this->metodo) {
            'efectivo'      => 'Efectivo',
            'tarjeta'       => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'yappy'         => 'Yappy / Nequi',
            'credito'       => 'Crédito',
            default         => $this->metodo,
        };
    }

    public function getMetodoBadgeColorAttribute(): string
    {
        return match($this->metodo) {
            'efectivo'      => 'emerald',
            'tarjeta'       => 'blue',
            'transferencia' => 'purple',
            'yappy'         => 'amber',
            'credito'       => 'red',
            default         => 'gray',
        };
    }
}
