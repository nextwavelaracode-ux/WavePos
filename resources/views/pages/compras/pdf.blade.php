<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte de Compra #{{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}</title>
    <!-- Incluir Tailwind CSS desde su CDN para que Spatie/laravel-pdf lo pueda procesar facilmente -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { font-size: 12pt; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-white text-gray-800 p-8 font-sans">

    <!-- Header -->
    <div class="flex justify-between items-start border-b-2 border-gray-200 pb-6 mb-6">
        <div>
            <!-- Reemplazar con el logo del sistema real -->
            <h1 class="text-3xl font-extrabold text-blue-800 tracking-tight">POS System</h1>
            <p class="text-sm text-gray-500 mt-1">Soporte de Adquisición a Proveedor</p>
            <p class="text-sm text-gray-500 mt-1">Sucursal: <span class="font-semibold">{{ $compra->sucursal->nombre }}</span></p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-gray-700">COMPRA #{{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <div class="inline-block mt-2 px-3 py-1 rounded bg-gray-100 text-sm font-semibold text-gray-600 uppercase">
                Estado: {{ $compra->estado }}
            </div>
            <p class="text-sm text-gray-500 mt-4">Fecha: <span class="font-semibold text-gray-700">{{ $compra->fecha_compra->format('d/m/Y') }}</span></p>
        </div>
    </div>

    <!-- Info Sections -->
    <div class="flex justify-between mb-8">
        <div class="w-1/2 pr-4">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Datos del Proveedor</h3>
            <p class="font-bold text-gray-800 text-lg">{{ $compra->proveedor->empresa }}</p>
            @if($compra->proveedor->ruc)
                <p class="text-sm text-gray-600">RUC: {{ $compra->proveedor->ruc }}</p>
            @endif
            @if($compra->proveedor->contacto)
                <p class="text-sm text-gray-600">Contacto: {{ $compra->proveedor->contacto }}</p>
            @endif
            @if($compra->proveedor->email)
                <p class="text-sm text-gray-600">Email: {{ $compra->proveedor->email }}</p>
            @endif
        </div>
        <div class="w-1/2 pl-4 border-l border-gray-200">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Detalles de Facturación</h3>
            <p class="text-sm text-gray-600 block mb-1">Nº Factura Proveedor: <span class="font-semibold text-gray-800">{{ $compra->numero_factura }}</span></p>
            <p class="text-sm text-gray-600 block mb-1">Tipo de Compra: <span class="font-semibold text-gray-800 uppercase">{{ $compra->tipo_compra }}</span></p>
            @if($compra->tipo_compra === 'credito' && $compra->fecha_vencimiento)
                <p class="text-sm text-gray-600 block mb-1">Vencimiento: <span class="font-semibold text-gray-800">{{ $compra->fecha_vencimiento->format('d/m/Y') }}</span></p>
            @endif
            <p class="text-sm text-gray-600 block mb-1">Registrado por: <span class="font-semibold text-gray-800">{{ $compra->usuario->name }}</span></p>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-8">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-200">
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">#</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Descripción / Producto</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase text-center">Cant.</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase text-right">Precio Unit.</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compra->detalles as $index => $detalle)
                    <tr class="border-b border-gray-100">
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-sm text-gray-800 font-medium">
                            {{ $detalle->producto->nombre }}
                            <span class="block text-xs text-gray-400 font-normal">SKU: {{ $detalle->producto->sku ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600 text-center">{{ $detalle->cantidad }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600 text-right">${{ number_format($detalle->precio_compra, 2) }}</td>
                        <td class="py-3 px-4 text-sm text-gray-800 font-bold text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="flex flex-col items-end mt-6">
        <div class="w-1/3 min-w-[250px] space-y-3">
            <div class="flex justify-between border-t-2 border-gray-800 pt-3">
                <span class="font-bold text-gray-800 text-lg">TOTAL:</span>
                <span class="font-bold text-blue-600 text-xl">${{ number_format($compra->total, 2) }}</span>
            </div>
        </div>
    </div>

    @if($compra->observaciones)
    <!-- Notas -->
    <div class="mt-12 pt-6 border-t border-gray-200">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Observaciones</h3>
        <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $compra->observaciones }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="fixed bottom-0 left-0 w-full text-center text-xs text-gray-400 pb-4">
        Documento generado automáticamente por el Sistema POS el {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
