<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use SoftDeletes;

    protected $table = 'gastos';

    protected $fillable = [
        'categoria_gasto_id',
        'sucursal_id',
        'user_id',
        'monto',
        'metodo_pago',
        'referencia',
        'fecha',
        'descripcion',
        'comprobante',
        'es_recurrente',
        'frecuencia',
        'fecha_programada',
        'estado',
        'notas_anulacion',
    ];

    protected $casts = [
        'monto'          => 'decimal:2',
        'fecha'          => 'date',
        'fecha_programada' => 'date',
        'es_recurrente'  => 'boolean',
    ];

    // Relations
    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helpers
    public function getMetodoPagoLabelAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo'      => 'Efectivo',
            'transferencia' => 'Transferencia',
            'tarjeta'       => 'Tarjeta',
            'cheque'        => 'Cheque',
            'yappy'         => 'Yappy/Nequi',
            default         => ucfirst($this->metodo_pago),
        };
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'activo'  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            'anulado' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
            default   => 'bg-gray-100 text-gray-700',
        };
    }

    public function getMetodoBadgeAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo'      => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
            'transferencia' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
            'tarjeta'       => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
            'cheque'        => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
            'yappy'         => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
            default         => 'bg-gray-100 text-gray-700',
        };
    }
}
