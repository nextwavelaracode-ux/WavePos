<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Services\WooCommerceService;
use Illuminate\Console\Command;

class WooCommerceTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'woocommerce:test {--sync-all : Sincronizar todos los productos con WooCommerce}';

    /**
     * The console command description.
     */
    protected $description = 'Probar conexión con WooCommerce y sincronizar productos';

    /**
     * Execute the console command.
     */
    public function handle(WooCommerceService $wcService)
    {
        $this->info('==================================================');
        $this->info('Probando conexión con WooCommerce local (tienda.test)...');
        $this->info('==================================================');

        $result = $wcService->testConnection();

        if (!$result['success']) {
            $this->error('ERROR: ' . $result['message']);
            return 1;
        }

        $this->info('✔ ' . $result['message']);
        if (isset($result['version'])) {
            $this->line('Versión de WooCommerce: ' . $result['version']);
        }
        if (isset($result['store'])) {
            $this->line('URL de la tienda: ' . $result['store']);
        }

        // Si se pide sincronizar todo
        if ($this->option('sync-all')) {
            $productos = Producto::where('estado', true)->get();
            $this->info("\nIniciando sincronización de {$productos->count()} productos...");

            $bar = $this->output->createProgressBar($productos->count());
            $bar->start();

            $successCount = 0;
            $failCount = 0;

            foreach ($productos as $producto) {
                $syncResult = $wcService->syncProduct($producto);
                if ($syncResult['success']) {
                    $successCount++;
                } else {
                    $failCount++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("Sincronización completada: {$successCount} exitosos, {$failCount} fallidos.");
        } else {
            // Probar sincronización con un producto existente
            $producto = Producto::first();
            if ($producto) {
                $this->info("\nProbando sincronización del producto: [{$producto->id}] {$producto->nombre} (Stock: {$producto->stock}, Precio: {$producto->precio_venta})");
                $syncResult = $wcService->syncProduct($producto);

                if ($syncResult['success']) {
                    $this->info("✔ Producto sincronizado correctamente en WooCommerce!");
                    $this->line("Acción: " . $syncResult['action']);
                    $this->line("ID en WooCommerce: " . $syncResult['woocommerce_id']);
                } else {
                    $this->error("Error al sincronizar producto: " . $syncResult['message']);
                }
            } else {
                $this->warn("\nNo hay productos en la base de datos de WavePos para sincronizar.");
            }
        }

        return 0;
    }
}
