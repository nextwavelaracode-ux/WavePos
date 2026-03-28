<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $venta->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #1f2937; background: #fff; }
        .page { max-width: 800px; margin: 0 auto; padding: 32px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 2px solid #3b82f6; padding-bottom: 16px; }
        .empresa-name { font-size: 22px; font-weight: 800; color: #1d4ed8; }
        .empresa-sub { color: #6b7280; font-size: 12px; margin-top: 4px; }
        .factura-badge { text-align: right; }
        .factura-badge .num { font-size: 20px; font-weight: 800; color: #1d4ed8; }
        .factura-badge .fecha { color: #6b7280; font-size: 12px; margin-top: 4px; }
        .estado-anulada { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
        .info-box h4 { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 8px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .info-label { color: #6b7280; }
        .info-val { font-weight: 600; color: #111827; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background: #1d4ed8; color: white; }
        thead th { padding: 10px 12px; font-size: 11px; font-weight: 600; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; }
        thead th:last-child, thead th:nth-last-child(2), thead th:nth-last-child(3) { text-align: right; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child { border-bottom: 2px solid #e5e7eb; }
        tbody td { padding: 9px 12px; }
        tbody td:last-child, tbody td:nth-last-child(2), tbody td:nth-last-child(3) { text-align: right; }
        tfoot td { padding: 6px 12px; }
        tfoot tr:last-child td { font-size: 16px; font-weight: 800; color: #1d4ed8; padding-top: 10px; }

        .totals { display: flex; justify-content: flex-end; }
        .totals-inner { width: 280px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; color: #374151; }
        .total-row.grand { font-size: 18px; font-weight: 800; color: #1d4ed8; border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 4px; }

        .pagos { margin-top: 20px; }
        .pagos h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 8px; }
        .pago-pill { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #e5e7eb; border-radius: 20px; padding: 5px 12px; margin: 3px; font-size: 12px; }
        .pago-pill .label { color: #6b7280; }
        .pago-pill .val { font-weight: 700; color: #111827; }

        .footer { margin-top: 32px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }
        .footer strong { color: #374151; }

        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Botón imprimir (solo pantalla) --}}
    <div class="no-print" style="margin-bottom:16px; text-align:right;">
        <button onclick="window.print()" style="background:#1d4ed8;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;">🖨 Imprimir</button>
    </div>

    {{-- Header --}}
    <div class="header">
        <div style="display: flex; gap: 16px; align-items: center;">
            @if($empresa && $empresa->logo)
                <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" style="max-height: 60px; max-width: 150px; object-fit: contain;">
            @endif
            <div>
                <div class="empresa-name">{{ $empresa?->nombre ?? config('app.name') }}</div>
                @if($empresa)
                    <div class="empresa-sub">
                        {{ $empresa->ruc ? 'RUC: ' . $empresa->ruc : '' }}<br>
                        {{ $empresa->direccion ?? '' }}<br>
                        {{ $empresa->telefono ? 'Tel: ' . $empresa->telefono : '' }}
                        {{ $empresa->email ? '· ' . $empresa->email : '' }}
                    </div>
                @endif
            </div>
        </div>
        <div class="factura-badge">
            <div style="font-size:11px;text-transform:uppercase;color:#9ca3af;letter-spacing:0.1em;">FACTURA</div>
            <div class="num">{{ $venta->numero }}</div>
            <div class="fecha">{{ $venta->fecha->format('d/m/Y') }}</div>
            @if($venta->estado === 'anulada')
                <div class="estado-anulada" style="margin-top:4px;">ANULADA</div>
            @endif
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="info-grid">
        <div class="info-box">
            <h4>Datos del Cliente</h4>
            <div class="info-row">
                <span class="info-label">Cliente</span>
                <span class="info-val">{{ $venta->cliente?->nombre_completo ?? 'Consumidor Final' }}</span>
            </div>
            @if($venta->cliente)
            <div class="info-row">
                <span class="info-label">Documento</span>
                <span class="info-val">{{ $venta->cliente->documento_principal }}</span>
            </div>
            @if($venta->cliente->telefono)
            <div class="info-row">
                <span class="info-label">Teléfono</span>
                <span class="info-val">{{ $venta->cliente->telefono }}</span>
            </div>
            @endif
            @endif
        </div>
        <div class="info-box">
            <h4>Información de Emisión</h4>
            <div class="info-row">
                <span class="info-label">Sucursal</span>
                <span class="info-val">{{ $venta->sucursal?->nombre ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cajero</span>
                <span class="info-val">{{ $venta->usuario?->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha</span>
                <span class="info-val">{{ $venta->fecha->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Productos --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Descripción</th>
                <th>Cant.</th>
                <th>Precio Unit.</th>
                <th>ITBMS</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->producto?->nombre ?? '—' }}</td>
                <td style="text-align:center;">{{ $d->cantidad }}</td>
                <td>${{ number_format($d->precio_unitario, 2) }}</td>
                <td>{{ $d->impuesto }}%</td>
                <td>${{ number_format($d->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="totals">
        <div class="totals-inner">
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format($venta->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>ITBMS (7%)</span>
                <span>${{ number_format($venta->itbms, 2) }}</span>
            </div>
            <div class="total-row grand">
                <span>TOTAL</span>
                <span>${{ number_format($venta->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Pagos --}}
    <div class="pagos">
        <h4>Pagos Recibidos</h4>
        @foreach($venta->pagos as $pago)
        <span class="pago-pill">
            <span class="label">{{ $pago->metodo_label }}</span>
            @if($pago->referencia) <span class="label">Ref: {{ $pago->referencia }}</span> @endif
            <span class="val">${{ number_format($pago->monto, 2) }}</span>
        </span>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p><strong>Gracias por su compra</strong> · Este documento es válido como comprobante de pago</p>
        <p style="margin-top:4px;">{{ $empresa?->nombre ?? config('app.name') }} · Panamá · {{ $venta->fecha->format('d/m/Y H:i') }}</p>
    </div>

</div>
</body>
</html>
