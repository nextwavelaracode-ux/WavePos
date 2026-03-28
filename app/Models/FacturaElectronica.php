<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaElectronica extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'venta_id',
        'factus_id',
        'numero',
        'cufe',
        'qr',
        'status',
        'total',
        'json_request',
        'json_response',
        'api_errors'
    ];

    protected $casts = [
        'json_request' => 'array',
        'json_response' => 'array',
        'api_errors' => 'array',
        'total' => 'decimal:2',
    ];

    /**
     * Get the Venta associated with this electronic invoice.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
