<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'proveedores';

    protected $fillable = [
        'empresa',
        'ruc',
        'dv',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'provincia',
        'ciudad',
        'pais',
        'notas',
        'estado',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }
}
