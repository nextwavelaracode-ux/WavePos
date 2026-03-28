<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $venta->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #e5e7eb; /* Fondo gris para la vista web */
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 10px;
        }
        .ticket {
            background: #fff;
            width: 80mm;
            padding: 15px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            /* Opcional: Borde dentado para que parezca cortado */
            border-bottom: 2px dashed #d1d5db;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        .header { margin-bottom: 10px; }
        .logo { max-width: 120px; max-height: 60px; margin-bottom: 5px; object-fit: contain; }
        
        .empresa-name { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
        .empresa-info { font-size: 11px; line-height: 1.2; }
        
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .divider-solid { border-top: 1px solid #000; margin: 8px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; font-size: 11px; vertical-align: top; }
        th { text-align: left; border-bottom: 1px dashed #000; padding-bottom: 4px; }
        
        /* Layout: Qty | Desc | Total */
        .col-qty { width: 15%; text-align: center; }
        .col-desc { width: 60%; text-align: left; padding-right: 5px; }
        .col-total { width: 25%; text-align: right; }
        
        .totals-container { margin-top: 10px; }
        .totals-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 3px; }
        .totals-row.grand-total { font-size: 15px; font-weight: bold; margin-top: 5px; padding-top: 5px; border-top: 1px dashed #000; }
        
        .pagos-container { margin-top: 10px; font-size: 11px; }
        .pago-row { display: flex; justify-content: space-between; }
        
        .footer { margin-top: 20px; text-align: center; font-size: 11px; }
        
        @media print {
            @page {
                size: 80mm 297mm; /* Altura generosa para rollo */
                margin: 0;
            }
            body { 
                print-color-adjust: exact; 
                -webkit-print-color-adjust: exact; 
                background: #fff;
                width: 80mm;
                margin: 0;
                padding: 0;
                display: block;
            }
            .ticket {
                width: 80mm;
                max-width: 80mm;
                padding: 2mm;
                margin: 0; /* Alinear arriba a la izquierda para impresión */
                box-shadow: none;
                border-bottom: none;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="ticket">

    {{-- Header --}}
    <div class="header text-center">
        @if($empresa && $empresa->logo)
            <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" class="logo">
        @endif
        <div class="empresa-name">{{ $empresa?->nombre ?? config('app.name') }}</div>
        @if($empresa)
            <div class="empresa-info">
                @if($empresa->ruc) RUC: {{ $empresa->ruc }}<br> @endif
                @if($empresa->direccion) {{ $empresa->direccion }}<br> @endif
                @if($empresa->telefono) Tel: {{ $empresa->telefono }}<br> @endif
                @if($empresa->email) {{ $empresa->email }} @endif
            </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Info Recibo --}}
    <div class="empresa-info">
        <div><span class="bold">TICKET:</span> {{ $venta->numero }}</div>
        <div><span class="bold">FECHA:</span> {{ $venta->fecha->format('d/m/Y H:i') }}</div>
        <div><span class="bold">CAJERO:</span> {{ $venta->usuario?->name ?? '—' }}</div>
        <div><span class="bold">CLIENTE:</span> {{ $venta->cliente?->nombre_completo ?? 'Consumidor Final' }}</div>
        @if($venta->cliente && $venta->cliente->documento_principal)
            <div><span class="bold">DOC CI:</span> {{ $venta->cliente->documento_principal }}</div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Productos --}}
    <table>
        <thead>
            <tr>
                <th class="col-qty">Cant</th>
                <th class="col-desc">Descripción</th>
                <th class="col-total">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $d)
            <tr>
                <td class="col-qty">{{ $d->cantidad }}</td>
                <td class="col-desc">
                    {{ $d->producto?->nombre ?? '—' }}<br>
                    <small>${{ number_format($d->precio_unitario, 2) }} c/u</small>
                </td>
                <td class="col-total">${{ number_format($d->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="totals-container">
        <div class="totals-row">
            <span>Subtotal</span>
            <span>${{ number_format($venta->subtotal, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>ITBMS (7%)</span>
            <span>${{ number_format($venta->itbms, 2) }}</span>
        </div>
        <div class="totals-row grand-total">
            <span>TOTAL</span>
            <span>${{ number_format($venta->total, 2) }}</span>
        </div>
    </div>

    <div class="divider"></div>

    {{-- Pagos --}}
    <div class="pagos-container">
        <div class="text-center bold" style="margin-bottom: 3px;">PAGOS RECIBIDOS</div>
        @foreach($venta->pagos as $pago)
        <div class="pago-row">
            <span>
                {{ strtoupper($pago->metodo_label) }}
                @if($pago->referencia) <small>(Ref:{{ $pago->referencia }})</small> @endif
            </span>
            <span>${{ number_format($pago->monto, 2) }}</span>
        </div>
        @endforeach
    </div>

    <div class="divider"></div>

    {{-- DIAN Info --}}
    @if($venta->facturaElectronica)
    <div class="pagos-container" style="text-align: center;">
        <div class="bold" style="margin-bottom: 3px; font-size: 12px;">FACTURA ELECTRÓNICA DIAN</div>
        <div style="font-size: 10px; line-height: 1.3;">
            Factura: <span class="bold">{{ $venta->facturaElectronica->numero }}</span><br>
            Estado: {{ $venta->facturaElectronica->status }}<br>
            @if($venta->facturaElectronica->cufe)
                CUFE: {{ substr($venta->facturaElectronica->cufe, 0, 25) }}...
            @endif
        </div>
    </div>
    <div class="divider"></div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p class="bold" style="font-size: 13px; margin-bottom: 5px;">¡Gracias por su compra!</p>
        <p>Este documento es un comprobante de pago.</p>
        <p style="margin-top: 5px;">****</p>
    </div>

</div>
</body>
</html>
