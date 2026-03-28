<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'numero',
        'caja_id',
        'sucursal_id',
        'cliente_id',
        'user_id',
        'subtotal',
        'itbms',
        'total',
        'estado',
        'motivo_anulacion',
        'fecha',
        'forma_pago_dian',
        'metodo_pago_dian',
        'fecha_vencimiento_dian',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'itbms'    => 'decimal:2',
        'total'    => 'decimal:2',
        'fecha'    => 'date',
    ];

    // Relations
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoVenta::class);
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class);
    }

    public function cuentaPorCobrar()
    {
        return $this->hasOne(CuentaPorCobrar::class);
    }

    public function facturaElectronica()
    {
        return $this->hasOne(FacturaElectronica::class);
    }

    // Helpers
    public function getEstadoBadgeColorAttribute(): string
    {
        return match($this->estado) {
            'completada' => 'emerald',
            'anulada'    => 'red',
            'espera'     => 'amber',
            default      => 'gray',
        };
    }

    public function getPagoResumenAttribute(): string
    {
        $metodos = $this->pagos->pluck('metodo')->unique()->implode(', ');
        return $metodos ?: '—';
    }

    /**
     * Generate next sale number: VTA-YYYYNNNNN
     */
    public static function generateNumero(): string
    {
        $year  = now()->format('Y');
        $last  = static::whereYear('created_at', $year)->lockForUpdate()->max('id');
        $seq   = str_pad(($last ? $last + 1 : 1), 5, '0', STR_PAD_LEFT);
        return "VTA-{$year}{$seq}";
    }
}
