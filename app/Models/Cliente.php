<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'clientes';

    protected $fillable = [
        'tipo_cliente',
        'nombre',
        'apellido',
        'empresa',
        'cedula',
        'ruc',
        'dv',
        'pasaporte',
        'telefono',
        'email',
        'direccion',
        'provincia',
        'distrito',
        'pais',
        'limite_credito',
        'notas',
        'estado',
        'tipo_documento_dian_id',
        'tipo_organizacion_dian_id',
        'tributo_dian_id',
        'municipio_dian_id',
    ];

    protected $casts = [
        'estado'          => 'boolean',
        'limite_credito'  => 'decimal:2',
    ];

    /**
     * Nombre para mostrar (natural: nombre + apellido, empresa: empresa, etc.)
     */
    public function getNombreCompletoAttribute(): string
    {
        return match($this->tipo_cliente) {
            'juridico', 'b2b' => $this->empresa ?? ($this->nombre . ' ' . $this->apellido),
            'extranjero'      => ($this->nombre ? $this->nombre . ' ' . $this->apellido : $this->empresa) ?? '',
            default           => trim($this->nombre . ' ' . $this->apellido),
        };
    }

    /**
     * Documento principal según tipo
     */
    public function getDocumentoPrincipalAttribute(): string
    {
        return match($this->tipo_cliente) {
            'juridico', 'b2b' => $this->ruc ? 'RUC: ' . $this->ruc . ($this->dv ? '-' . $this->dv : '') : '—',
            'extranjero'      => $this->pasaporte ? 'Pasaporte: ' . $this->pasaporte : '—',
            default           => $this->cedula ? 'Céd: ' . $this->cedula : '—',
        };
    }

    /**
     * Label del tipo de cliente
     */
    public static function tiposCliente(): array
    {
        return [
            'natural'    => 'Cliente Natural',
            'juridico'   => 'Cliente Jurídico',
            'extranjero' => 'Cliente Extranjero',
            'b2b'        => 'Cliente B2B',
            'b2c'        => 'Cliente B2C',
        ];
    }

    public function cuentasCobrar()
    {
        return $this->hasMany(CuentaPorCobrar::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
