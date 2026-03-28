<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Gastos</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .total { text-align: right; margin-top: 10px; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Gastos</h1>
        <p>Rango: {{ $fecha_desde ?? 'Inicio' }} al {{ $fecha_hasta ?? 'Fin' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Método</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($gastos as $gasto)
                @php $total += $gasto->monto; @endphp
                <tr>
                    <td>#{{ $gasto->id }}</td>
                    <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                    <td>{{ $gasto->categoria?->nombre }}</td>
                    <td>{{ $gasto->descripcion }}</td>
                    <td>{{ $gasto->metodo_pago_label }}</td>
                    <td>${{ number_format($gasto->monto, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        TOTAL GASTOS: ${{ number_format($total, 2) }}
    </div>
</body>
</html>
