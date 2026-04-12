@extends('layouts.app')

@php
    $title = 'Factura - ' . ($factura->numero !== 'N/A' ? $factura->numero : 'Pendiente');

    $esValidada     = $factura->status === 'Validado';
    $cufe           = $factura->cufe ?? null;
    $qrPath         = $factura->qr ?? null;
    $items          = [];
    $companyLogoUrl = null;
    $qrImageSrc     = null; // el src completo para <img>, puede ser data:image o url

    if ($factura->json_response) {
        $data = is_string($factura->json_response)
            ? json_decode($factura->json_response, true)
            : $factura->json_response;

        $dianBill       = $data['data']['bill']             ?? null;
        $companyLogoUrl = $data['data']['company']['url_logo'] ?? null;

        // qr_image ya viene con el prefijo "data:image/png;base64," incluido
        $qrRaw = $dianBill['qr_image'] ?? null;
        if ($qrRaw) {
            $qrImageSrc = $qrRaw; // usar directo
        } elseif ($qrPath) {
            // generar QR desde la URL DIAN con servicio externo
            $qrImageSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrPath);
        }
    }

    if ($factura->json_request) {
        $req   = is_string($factura->json_request)
            ? json_decode($factura->json_request, true)
            : $factura->json_request;
        $items = $req['items'] ?? [];
    }

    $cliente       = $factura->venta->cliente ?? null;

    // Fallback: extraer datos del cliente desde json_request (facturas manuales sin venta asociada)
    if (!$cliente && $factura->json_request) {
        $reqData = is_string($factura->json_request)
            ? json_decode($factura->json_request, true)
            : $factura->json_request;
        $customerReq = $reqData['customer'] ?? null;
        if ($customerReq) {
            $nombreCliente = $customerReq['names'] ?? ($customerReq['company'] ?? 'Consumidor Final');
            $docCliente    = $customerReq['identification'] ?? 'N/A';
            $emailCliente  = $customerReq['email']    ?? 'N/A';
            $dirCliente    = $customerReq['address']  ?? 'N/A';
        } else {
            $nombreCliente = 'Consumidor Final';
            $docCliente    = 'N/A';
            $emailCliente  = 'N/A';
            $dirCliente    = 'N/A';
        }
    } else {
        $nombreCliente = trim((($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))
            ?: ($cliente->empresa ?? 'Consumidor Final'));
        $docCliente    = $cliente->cedula  ?? $cliente->ruc ?? $cliente->pasaporte ?? 'N/A';
        $emailCliente  = $cliente->email   ?? 'N/A';
        $dirCliente    = $cliente->direccion ?? 'N/A';
    }

    $subtotal = collect($items)->sum(fn($i) => ($i['quantity'] ?? 1) * ($i['price'] ?? 0));
    $tax      = collect($items)->sum(fn($i) => ($i['quantity'] ?? 1) * ($i['price'] ?? 0) * (floatval($i['tax_rate'] ?? 0) / 100));
    $totalFinal = $factura->total ?? ($subtotal + $tax);
@endphp

@section('content')
<div class="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto my-4 sm:my-10">

    {{-- ── Barra superior ──────────────────────────────── --}}
    <div class="mb-5 pb-5 flex justify-between items-center border-b border-gray-200 dark:border-neutral-700">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-neutral-200">Factura</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">
                #{{ $factura->numero !== 'N/A' ? $factura->numero : $factura->id }}
                &nbsp;·&nbsp;
                @if($esValidada)
                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Validado DIAN
                    </span>
                @else
                    <span class="text-red-500 font-medium">{{ $factura->status }}</span>
                @endif
            </p>
        </div>

        <div class="inline-flex gap-x-2">
            @if($esValidada)
            <button onclick="enviarEmail()"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-white shadow-2xs hover:bg-gray-50 dark:hover:bg-neutral-700">
                <svg class="shrink-0 size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Enviar
            </button>
            <a href="{{ route('facturacion.pdf', $factura->id) }}" target="_blank"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-white shadow-2xs hover:bg-gray-50 dark:hover:bg-neutral-700">
                <svg class="shrink-0 size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Invoice PDF
            </a>
            @endif
            <a href="{{ route('facturacion.index') }}"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 dark:bg-blue-500 border border-transparent text-white hover:bg-blue-700 dark:hover:bg-blue-600">
                <svg class="shrink-0 size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                Ver todas
            </a>
        </div>
    </div>

    {{-- ── Logo grande + QR en la cabecera de la factura ────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">

        {{-- Logo grande --}}
        <div class="flex flex-col items-start gap-2">
            @if(!empty($companyLogoUrl))
                <img src="{{ $companyLogoUrl }}" alt="Logo Empresa" class="h-16 w-auto object-contain">
            @endif
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Operador Tecnológico — DIAN</p>
        </div>

        {{-- QR DIAN grande y visible --}}
        @if($qrImageSrc)
        <div class="flex flex-col items-center gap-2">
            <img src="{{ $qrImageSrc }}" alt="Código QR DIAN" style="width:140px; height:140px; object-fit:contain;">
            <p class="text-xs text-gray-500 dark:text-neutral-400 text-center max-w-[140px]">Escanea para verificar en la DIAN</p>
        </div>
        @endif
    </div>

    {{-- ── Grid datos cliente / factura ─────────────────── --}}
    <div class="grid md:grid-cols-2 gap-3">

        <div class="grid space-y-3">
            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">Cobrado a:</dt>
                <dd class="text-gray-800 dark:text-neutral-200">
                    <a class="inline-flex items-center gap-x-1.5 text-blue-600 dark:text-blue-500 decoration-2 hover:underline font-medium" href="mailto:{{ $emailCliente }}">
                        {{ $emailCliente }}
                    </a>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">Datos de facturación:</dt>
                <dd class="font-medium text-gray-800 dark:text-neutral-200">
                    <span class="block font-semibold">{{ $nombreCliente }}</span>
                    <address class="not-italic font-normal text-gray-500 dark:text-neutral-400">
                        NIT/CC: {{ $docCliente }}<br>
                        {{ $dirCliente }}
                    </address>
                </dd>
            </dl>
        </div>

        <div class="grid space-y-3">
            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">Número de factura:</dt>
                <dd class="font-medium text-gray-800 dark:text-neutral-200">
                    {{ $factura->numero !== 'N/A' ? $factura->numero : ('FW-' . $factura->id) }}
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">Moneda:</dt>
                <dd class="font-medium text-gray-800 dark:text-neutral-200">COP - Peso Colombiano</dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">Fecha de emisión:</dt>
                <dd class="font-medium text-gray-800 dark:text-neutral-200">
                    {{ $factura->created_at->translatedFormat('d M Y') }}
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">Estado DIAN:</dt>
                <dd class="font-medium text-gray-800 dark:text-neutral-200">{{ $factura->status }}</dd>
            </dl>

            @if($cufe)
            <dl class="flex flex-col sm:flex-row gap-x-3 text-sm">
                <dt class="min-w-36 max-w-50 text-gray-500 dark:text-neutral-400">CUFE:</dt>
                <dd class="font-mono text-[11px] break-all text-gray-600 dark:text-neutral-400">{{ $cufe }}</dd>
            </dl>
            @endif
        </div>
    </div>

    {{-- ── Tabla artículos ───────────────────────────────── --}}
    <div class="mt-6 border border-gray-200 dark:border-neutral-700 p-4 rounded-lg space-y-4">
        <div class="hidden sm:grid sm:grid-cols-5">
            <div class="sm:col-span-2 text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Artículo</div>
            <div class="text-start text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Cant.</div>
            <div class="text-start text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Precio</div>
            <div class="text-end text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Importe</div>
        </div>

        <div class="hidden sm:block border-b border-gray-200 dark:border-neutral-700"></div>

        @forelse($items as $item)
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
            <div class="col-span-full sm:col-span-2">
                <h5 class="sm:hidden text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Artículo</h5>
                <p class="font-medium text-gray-800 dark:text-neutral-200">{{ $item['name'] ?? 'Producto' }}</p>
                <p class="text-xs text-gray-500 dark:text-neutral-500">Ref: {{ $item['code_reference'] ?? '-' }}</p>
            </div>
            <div>
                <h5 class="sm:hidden text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Cant.</h5>
                <p class="text-gray-800 dark:text-neutral-200">{{ $item['quantity'] ?? 1 }}</p>
            </div>
            <div>
                <h5 class="sm:hidden text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Precio</h5>
                <p class="text-gray-800 dark:text-neutral-200">${{ number_format($item['price'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <h5 class="sm:hidden text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Importe</h5>
                <p class="sm:text-end text-gray-800 dark:text-neutral-200">${{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}</p>
            </div>
        </div>
        @if(!$loop->last)
        <div class="sm:hidden border-b border-gray-200 dark:border-neutral-700"></div>
        @endif
        @empty
        <p class="text-sm text-gray-500 dark:text-neutral-400 text-center py-4">Sin artículos registrados.</p>
        @endforelse
    </div>

    {{-- ── Totales ───────────────────────────────────────── --}}
    <div class="mt-8 flex sm:justify-end">
        <div class="w-full max-w-2xl sm:text-end space-y-2">
            <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                    <dt class="col-span-3 text-gray-500 dark:text-neutral-400">Subtotal:</dt>
                    <dd class="col-span-2 font-medium text-gray-800 dark:text-neutral-200">${{ number_format($subtotal, 0, ',', '.') }}</dd>
                </dl>

                <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                    <dt class="col-span-3 text-gray-500 dark:text-neutral-400">Impuestos (IVA est.):</dt>
                    <dd class="col-span-2 font-medium text-gray-800 dark:text-neutral-200">${{ number_format($tax, 0, ',', '.') }}</dd>
                </dl>

                <dl class="grid sm:grid-cols-5 gap-x-3 text-sm border-t border-gray-200 dark:border-neutral-700 pt-2 mt-1">
                    <dt class="col-span-3 font-semibold text-gray-700 dark:text-neutral-300">Total pagado:</dt>
                    <dd class="col-span-2 font-bold text-gray-900 dark:text-white">${{ number_format($totalFinal, 0, ',', '.') }}</dd>
                </dl>
            </div>
        </div>
    </div>

</div>

<form id="emailForm" action="{{ route('facturacion.sendEmail', $factura->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="email" id="targetEmail" value="{{ $emailCliente !== 'N/A' ? $emailCliente : '' }}">
</form>

<script>
function enviarEmail() {
    const def = '{{ $emailCliente !== 'N/A' ? $emailCliente : '' }}';
    const email = prompt('Correo para enviar la factura:', def);
    if (email && email.trim()) {
        document.getElementById('targetEmail').value = email.trim();
        document.getElementById('emailForm').submit();
    }
}
</script>
@endsection
