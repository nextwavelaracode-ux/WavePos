<?php

/**
 * FacturaElectronica — Modelo Eloquent para Facturas Electrónicas DIAN
 *
 * Representa el registro persistente de cada factura electrónica emitida
 * a través de la plataforma Factus y validada por la DIAN.
 *
 * Características clave:
 *  - SoftDeletes: Las facturas NUNCA se eliminan físicamente (auditoría tributaria).
 *  - Los campos json_request / json_response guardan el log completo de la
 *    comunicación con Factus para debugging y auditoría.
 *  - La relación con Venta es nullable (se puede facturar sin venta previa).
 *
 * Tabla: factura_electronicas
 *
 * @property int         $id
 * @property int|null    $venta_id       FK opcional a la venta del POS
 * @property string|null $factus_id      ID interno asignado por Factus
 * @property string|null $numero         Número de factura DIAN (ej: "SETT-1")
 * @property string|null $cufe           Código Único de Factura Electrónica
 * @property string|null $qr             URL del código QR de verificación DIAN
 * @property string      $status         Estado: "Validado" | "Pendiente"
 * @property float       $total          Valor total de la factura en COP
 * @property array|null  $json_request   Payload enviado a Factus (auto-cast JSON)
 * @property array|null  $json_response  Respuesta completa de Factus (auto-cast JSON)
 * @property array|null  $api_errors     Errores registrados en caso de fallo
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at  SoftDelete timestamp
 *
 * @package App\Models
 * @author  WavePOS — NextWave
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaElectronica extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Campos asignables en masa.
     * Todos los campos de integración con la API de Factus están incluidos.
     */
    protected $fillable = [
        'venta_id',      // FK a la venta del POS (nullable)
        'factus_id',     // ID interno de Factus
        'numero',        // Número de factura asignado por DIAN (ej: "SETT-1")
        'cufe',          // Código Único de Factura Electrónica — identificador fiscal
        'qr',            // URL del QR de verificación en el portal DIAN
        'status',        // "Validado" | "Pendiente"
        'total',         // Total de la factura en pesos colombianos (COP)
        'json_request',  // Payload completo enviado a Factus (log de auditoría)
        'json_response', // Respuesta completa de la API de Factus (log de auditoría)
        'api_errors'     // Errores retornados en caso de fallo de validación
    ];

    /**
     * Cast automático de tipos.
     * Los campos JSON se deserializan automáticamente a arrays PHP al leerlos.
     */
    protected $casts = [
        'json_request'  => 'array',
        'json_response' => 'array',
        'api_errors'    => 'array',
        'total'         => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // RELACIONES ELOQUENT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Relación: una factura electrónica pertenece a una Venta del POS.
     *
     * La relación es nullable — una factura puede ser "manual" y no tener
     * una venta asociada (ej: facturas por servicios o ventas externas).
     *
     * Uso: $factura->venta->cliente->nombre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
