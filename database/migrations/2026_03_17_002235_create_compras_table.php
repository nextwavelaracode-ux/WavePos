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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // Usuario que registró
            
            $table->string('numero_factura');
            $table->date('fecha_compra');
            
            $table->enum('tipo_compra', ['contado', 'credito']);
            $table->string('metodo_pago')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['registrada', 'anulada', 'devuelta'])->default('registrada');
            $table->text('observaciones')->nullable();

            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index('fecha_compra');
            $table->index('numero_factura');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
