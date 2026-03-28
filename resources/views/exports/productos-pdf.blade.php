<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Productos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Productos</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Categoría</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $prod)
                <tr>
                    <td>{{ $prod->nombre }}</td>
                    <td>{{ $prod->sku }}</td>
                    <td>{{ $prod->categoria?->nombre }}</td>
                    <td>${{ number_format($prod->precio_venta, 2) }}</td>
                    <td>{{ $prod->stock }}</td>
                    <td>{{ $prod->estado ? 'Activo' : 'Inactivo' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Página <script type="text/php">if (isset($pdf)) { $text = "{PAGE_NUM} / {PAGE_COUNT}"; $size = 10; $font = $fontMetrics->getFont("Verdana"); $width = $fontMetrics->get_text_width($text, $font, $size); $pdf->page_text($w / 2 - $width / 2, $h - 20, $text, $font, $size); }</script>
    </div>
</body>
</html>
