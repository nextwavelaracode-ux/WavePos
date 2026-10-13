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
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedBigInteger('woocommerce_id')->nullable()->unique()->after('id');
            $table->boolean('sincronizar_woocommerce')->default(false)->after('estado');
            $table->timestamp('ultima_sincronizacion_wc')->nullable()->after('sincronizar_woocommerce');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->string('origen')->default('pos')->after('estado'); // 'pos' or 'woocommerce'
            $table->unsignedBigInteger('woocommerce_order_id')->nullable()->after('origen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['woocommerce_id', 'sincronizar_woocommerce', 'ultima_sincronizacion_wc']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['origen', 'woocommerce_order_id']);
        });
    }
};
