<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGasto extends Model
{
    protected $table = 'categorias_gasto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // Relations
    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }
}
