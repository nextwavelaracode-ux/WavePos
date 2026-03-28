<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');

            $table->enum('metodo', ['efectivo', 'tarjeta', 'transferencia', 'yappy', 'credito']);
            $table->decimal('monto', 10, 2);
            $table->string('referencia')->nullable();          // voucher o comprobante
            $table->enum('tipo_tarjeta', ['credito', 'debito'])->nullable();
            $table->string('banco')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_venta');
    }
};
