<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factura_electronicas', function (Blueprint $table) {
            $table->id();
            
            // Relación opcional a una venta del POS
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null');
            
            // Datos devueltos por Factus
            $table->string('factus_id', 255)->nullable()->comment('ID interno generado por Factus');
            $table->string('numero', 100)->nullable()->comment('Número de la factura (Ej: SETT1)');
            $table->string('cufe', 255)->nullable()->comment('Código Único de Factura Electrónica');
            $table->string('qr', 500)->nullable()->comment('URL al código QR de la DIAN');
            $table->string('status', 100)->default('Pendiente')->comment('Estado de la validación');
            $table->decimal('total', 15, 2)->default(0);
            
            // Logs de integración
            $table->json('json_request')->nullable()->comment('Payload enviado a Factus');
            $table->json('json_response')->nullable()->comment('Respuesta de Factus');
            $table->json('api_errors')->nullable()->comment('Errores devueltos en caso de fallo');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_electronicas');
    }
};
