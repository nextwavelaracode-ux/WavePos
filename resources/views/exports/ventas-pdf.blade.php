<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .row-anulada { text-decoration: line-through; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ventas</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Subtotal</th>
                <th>ITBMS</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @php $totalG = 0; @endphp
            @foreach($ventas as $venta)
                @if($venta->estado === 'completada')
                    @php $totalG += $venta->total; @endphp
                @endif
                <tr class="{{ $venta->estado === 'anulada' ? 'row-anulada' : '' }}">
                    <td>{{ $venta->numero }}</td>
                    <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                    <td>{{ $venta->cliente?->nombre_completo ?? 'Consumidor Final' }}</td>
                    <td>${{ number_format($venta->subtotal, 2) }}</td>
                    <td>${{ number_format($venta->itbms, 2) }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                    <td>{{ ucfirst($venta->estado) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 15px; font-size: 14px; font-weight: bold;">
        TOTAL VENTAS COMPLETADAS: ${{ number_format($totalG, 2) }}
    </div>
</body>
</html>
