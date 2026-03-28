<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();

            $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'transferencia', 'venta']);

            $table->enum('motivo', [
                'compra',
                'ajuste_manual',
                'devolucion',
                'transferencia',
                'venta',
                'producto_dañado',
                'devolucion_proveedor',
                'anulacion_compra',
            ]);

            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');

            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->string('numero_factura')->nullable();
            $table->text('observaciones')->nullable();

            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->date('fecha');

            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(['producto_id', 'tipo']);
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
