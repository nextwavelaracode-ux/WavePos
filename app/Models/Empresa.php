<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'ruc',
        'direccion',
        'telefono',
        'email',
        'logo',
        'moneda',
        'zona_horaria',
        // Numeración automática
        'prefijo_factura',
        'prefijo_compra',
        'ultimo_numero_factura',
        'ultimo_numero_compra',
        'digitos_correlativo',
    ];

    protected $casts = [
        'ultimo_numero_factura' => 'integer',
        'ultimo_numero_compra'  => 'integer',
        'digitos_correlativo'   => 'integer',
    ];

    /**
     * Obtener la instancia singleton de la empresa.
     */
    public static function instance(): static
    {
        return static::first() ?? static::create([
            'nombre'       => 'Mi Empresa',
            'moneda'       => 'USD',
            'zona_horaria' => 'America/Guayaquil',
        ]);
    }

    /**
     * Genera el siguiente número de COMPRA de forma atómica.
     * Incrementa el contador y devuelve el número formateado.
     */
    public static function nextNumeroCompra(): string
    {
        $empresa = static::instance();

        // Incremento atómico directo en la BD para evitar race conditions
        DB::table('empresas')->where('id', $empresa->id)
            ->increment('ultimo_numero_compra');

        $empresa->refresh();

        $padded = str_pad($empresa->ultimo_numero_compra, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT);
        return ($empresa->prefijo_compra ?? 'COMP-') . $padded;
    }

    /**
     * Genera el siguiente número de FACTURA MANUAL de forma atómica.
     */
    public static function nextNumeroFactura(): string
    {
        $empresa = static::instance();

        DB::table('empresas')->where('id', $empresa->id)
            ->increment('ultimo_numero_factura');

        $empresa->refresh();

        $padded = str_pad($empresa->ultimo_numero_factura, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT);
        return ($empresa->prefijo_factura ?? 'FACT-') . $padded;
    }

    /**
     * Preview del próximo número sin incrementar.
     */
    public static function previewNumeroCompra(): string
    {
        $empresa = static::instance();
        $next = ($empresa->ultimo_numero_compra ?? 0) + 1;
        $padded = str_pad($next, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT);
        return ($empresa->prefijo_compra ?? 'COMP-') . $padded;
    }

    public static function previewNumeroFactura(): string
    {
        $empresa = static::instance();
        $next = ($empresa->ultimo_numero_factura ?? 0) + 1;
        $padded = str_pad($next, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT);
        return ($empresa->prefijo_factura ?? 'FACT-') . $padded;
    }
}
