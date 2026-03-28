<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // Apertura
            $table->decimal('monto_inicial', 10, 2)->default(0);
            $table->dateTime('fecha_apertura');

            // Cierre
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('total_ventas', 10, 2)->default(0);
            $table->decimal('total_efectivo', 10, 2)->default(0);
            $table->decimal('total_tarjeta', 10, 2)->default(0);
            $table->decimal('total_transferencia', 10, 2)->default(0);
            $table->decimal('total_yappy', 10, 2)->default(0);
            $table->decimal('total_credito', 10, 2)->default(0);
            $table->decimal('monto_real_cierre', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable();

            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
