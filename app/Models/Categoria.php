<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'parent_id',
        'impuesto',
        'unidad_medida',
        'ubicacion',
        'atributos_tecnicos',
        'detalle',
        'imagen',
        'orden_visualizacion',
        'estado',
    ];

    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }
}
