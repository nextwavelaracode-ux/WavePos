<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'subcategoria_id',
        'sku',
        'codigo_barras',
        'precio_compra',
        'precio_venta',
        'precio_minimo',
        'margen',
        'impuesto',
        'stock',
        'stock_minimo',
        'stock_maximo',
        'unidad_medida',
        'ubicacion',
        'pasillo',
        'estante',
        'imagen',
        'estado',
        'tasa_impuesto',
        'is_excluded',
        'unidad_medida_dian_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
        'margen' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
    ];

    protected $appends = ['imagen_url'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function subcategoria()
    {
        return $this->belongsTo(Categoria::class, 'subcategoria_id');
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'producto_id');
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen && Storage::disk('public')->exists($this->imagen)) {
            return Storage::disk('public')->url($this->imagen);
        }
        return null;
    }
}
