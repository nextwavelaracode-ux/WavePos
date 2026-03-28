<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'sucursal_id',
        'user_id',
        'monto_inicial',
        'fecha_apertura',
        'fecha_cierre',
        'total_ventas',
        'total_efectivo',
        'total_tarjeta',
        'total_transferencia',
        'total_yappy',
        'total_credito',
        'monto_real_cierre',
        'diferencia',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_apertura'    => 'datetime',
        'fecha_cierre'      => 'datetime',
        'monto_inicial'     => 'decimal:2',
        'total_ventas'      => 'decimal:2',
        'total_efectivo'    => 'decimal:2',
        'total_tarjeta'     => 'decimal:2',
        'total_transferencia' => 'decimal:2',
        'total_yappy'       => 'decimal:2',
        'total_credito'     => 'decimal:2',
        'monto_real_cierre' => 'decimal:2',
        'diferencia'        => 'decimal:2',
    ];

    // Relations
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function pagosCuentasCobrar()
    {
        return $this->hasMany(PagoCuentaCobrar::class);
    }

    // Helpers
    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    public function getMontoEsperadoAttribute(): float
    {
        return (float) $this->monto_inicial + (float) $this->total_efectivo;
    }
}
