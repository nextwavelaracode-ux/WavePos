<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas_espera', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable(); // etiqueta del cajero
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->json('carrito');              // snapshot del carrito (producto_id, qty, precio...)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas_espera');
    }
};
