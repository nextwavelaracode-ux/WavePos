<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaEspera extends Model
{
    protected $table = 'ventas_espera';

    protected $fillable = [
        'nombre',
        'sucursal_id',
        'user_id',
        'carrito',
    ];

    protected $casts = [
        'carrito' => 'array',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTotalCarritoAttribute(): float
    {
        return collect($this->carrito)->sum(function ($item) {
            return ($item['precio_unitario'] ?? 0) * ($item['cantidad'] ?? 0);
        });
    }
}
