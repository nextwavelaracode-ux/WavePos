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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            
            // Información básica
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            
            // Identificación
            $table->string('sku')->nullable()->unique();
            $table->string('codigo_barras')->nullable()->unique();
            
            // Precios
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->decimal('precio_minimo', 10, 2)->default(0);
            $table->decimal('margen', 5, 2)->default(0); // Porcentaje de ganancia
            
            // Fiscal
            $table->enum('impuesto', ['0', '7', '10', '15'])->default('7');
            
            // Inventario
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->nullable();
            $table->string('unidad_medida')->default('unidad');
            
            // Ubicación
            $table->string('ubicacion')->nullable();
            $table->string('pasillo')->nullable();
            $table->string('estante')->nullable();
            
            // Media y Estado
            $table->string('imagen')->nullable();
            $table->boolean('estado')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
