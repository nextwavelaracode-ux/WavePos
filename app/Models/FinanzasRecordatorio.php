<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanzasRecordatorio extends Model
{
    protected $table = 'finanzas_recordatorios';

    protected $fillable = [
        'fecha',
        'titulo',
        'descripcion',
        'user_id',
        'color',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
