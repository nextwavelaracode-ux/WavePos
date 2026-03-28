<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'tipo',
        'motivo',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'proveedor_id',
        'precio_compra',
        'numero_factura',
        'observaciones',
        'usuario_id',
        'fecha',
    ];

    protected $casts = [
        'cantidad'       => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo'    => 'integer',
        'precio_compra'  => 'decimal:2',
        'fecha'          => 'date',
    ];

    // ── Relaciones ─────────────────────────────────

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ── Scopes útiles ──────────────────────────────

    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }

    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    // ── Helpers ────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'entrada'       => 'Entrada',
            'salida'        => 'Salida',
            'ajuste'        => 'Ajuste',
            'transferencia' => 'Transferencia',
            'venta'         => 'Venta',
            default         => $this->tipo,
        };
    }

    public function getMotivoLabelAttribute(): string
    {
        return match ($this->motivo) {
            'compra'               => 'Compra a proveedor',
            'ajuste_manual'        => 'Ajuste manual',
            'devolucion'           => 'Devolución',
            'transferencia'        => 'Transferencia',
            'venta'                => 'Venta',
            'producto_dañado'      => 'Producto dañado',
            'devolucion_proveedor' => 'Devolución a proveedor',
            default                => $this->motivo,
        };
    }

    public function getTipoBadgeColorAttribute(): string
    {
        return match ($this->tipo) {
            'entrada'       => 'emerald',
            'salida'        => 'red',
            'ajuste'        => 'amber',
            'transferencia' => 'blue',
            'venta'         => 'purple',
            default         => 'gray',
        };
    }
}
