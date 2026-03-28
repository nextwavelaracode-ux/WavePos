<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_compra', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'yappy_nequi', 'yappy', 'nequi']);
            $table->decimal('monto', 10, 2);
            $table->string('referencia')->nullable();
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_compra');
    }
};
