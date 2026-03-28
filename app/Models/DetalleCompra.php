<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compras';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_compra',
        'tasa_impuesto',
        'porcentaje_descuento',
        'monto_impuesto',
        'monto_descuento',
        'subtotal',
    ];

    protected $casts = [
        'cantidad'             => 'integer',
        'precio_compra'        => 'decimal:2',
        'tasa_impuesto'        => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'monto_impuesto'       => 'decimal:2',
        'monto_descuento'      => 'decimal:2',
        'subtotal'             => 'decimal:2',
    ];

    // ── Relaciones ─────────────────────────────────

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
