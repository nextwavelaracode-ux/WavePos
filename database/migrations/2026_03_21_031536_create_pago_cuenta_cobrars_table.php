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
        Schema::create('pagos_cuentas_cobrar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('cuentas_por_cobrar')->onDelete('cascade');
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->decimal('monto', 10, 2);
            $table->string('metodo', 50); // efectivo, tarjeta, transferencia, yappy
            $table->string('referencia')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_cuentas_cobrar');
    }
};
