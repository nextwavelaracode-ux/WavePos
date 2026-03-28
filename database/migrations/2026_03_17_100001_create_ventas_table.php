<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // VTA-202600001
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->onDelete('set null');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('restrict');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('itbms', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('estado', ['completada', 'anulada', 'espera'])->default('completada');
            $table->text('motivo_anulacion')->nullable();

            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
