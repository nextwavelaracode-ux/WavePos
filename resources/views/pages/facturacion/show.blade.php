@extends('layouts.app')
@php
    $title = 'Detalle de Factura DIAN';
@endphp

@section('content')

    {{-- Breadcrumb al estilo del sistema --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                Detalle Factura DIAN: <span class="text-primary">{{ $factura->numero }}</span>
            </h2>

            @if($factura->status === 'Validado')
                <span class="inline-flex items-center gap-1.5 rounded-md bg-success bg-opacity-10 px-3 py-1 text-sm font-medium text-success">
                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span> Validado DIAN
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-md bg-warning bg-opacity-10 px-3 py-1 text-sm font-medium text-warning">
                    {{ $factura->status }}
                </span>
            @endif
        </div>

        <nav>
            <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="{{ route('dashboard') }}">Dashboard /</a></li>
                <li><a class="font-medium" href="{{ route('facturacion.index') }}">Facturación /</a></li>
                <li class="font-medium text-primary">Detalle</li>
            </ol>
        </nav>
    </div>

    {{-- Actions --}}
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('facturacion.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
            <svg class="mr-2" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Volver
        </a>
        @if($factura->numero && $factura->numero !== 'N/A')
        <a href="{{ route('facturacion.pdf', $factura->id) }}" target="_blank"
           class="inline-flex items-center justify-center rounded-xl border border-primary bg-primary bg-opacity-10 px-5 py-2.5 text-center font-medium text-primary hover:bg-opacity-20 transition">
            <svg class="mr-2" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Imprimir PDF
        </a>
        @endif
        @if($factura->qr)
        <a href="{{ $factura->qr }}" target="_blank"
           class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
            <svg class="mr-2" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Ver en Portal DIAN
        </a>
        @endif
        @if($factura->numero && $factura->numero !== 'N/A')
        <div x-data="{ openEmail: false }" class="relative inline-block">
            <button @click="openEmail = !openEmail"
               class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                <svg class="mr-2" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Enviar por Correo
            </button>

            <div x-show="openEmail" @click.away="openEmail = false" x-transition
                 class="absolute left-0 mt-2 w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800 z-50">
                <form action="{{ route('facturacion.sendEmail', $factura->id) }}" method="POST">
                    @csrf
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Correo del Cliente
                    </label>
                    <input type="email" name="email" required placeholder="correo@empresa.com"
                           class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 mb-3 text-black dark:text-white"
                           value="{{ $factura->venta->cliente->email ?? '' }}">
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openEmail = false" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Cancelar</button>
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    @php
        $resp         = $factura->json_response ?? [];
        $billData     = $resp['data']['bill']       ?? null;
        $customerData = $resp['data']['customer']  ?? null;
        $companyData  = $resp['data']['company']   ?? null;
        $itemsData    = $resp['data']['items']     ?? [];
        $rangeData    = $resp['data']['numbering_range'] ?? null;
        $qrImage      = $resp['data']['bill']['qr_image'] ?? null;
    @endphp

    {{-- UN SOLO CARD CONTENEDOR PARA TODO (Igual que create.blade.php) --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900 p-6 lg:p-8">

        {{-- ────── 1. Resumen Emisor / Cabecera ────── --}}
        @if($companyData)
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <div class="flex items-center gap-4">
                    @if(!empty($companyData['url_logo']))
                        <img src="{{ $companyData['url_logo'] }}" alt="Logo Empresa" class="h-16 w-auto object-contain bg-white rounded">
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-black dark:text-white">{{ $companyData['company'] ?? 'Empresa' }}</h2>
                        <p class="text-sm text-gray-500 font-mono mt-1">NIT: {{ $companyData['nit'] }}-{{ $companyData['dv'] ?? '' }}</p>
                        <p class="text-sm text-gray-500">{{ $companyData['direction'] ?? '' }} | {{ $companyData['municipality'] ?? '' }}</p>
                        <p class="text-sm text-gray-500">{{ $companyData['phone'] ?? '' }} | {{ $companyData['email'] ?? '' }}</p>
                    </div>
                </div>
                <div class="md:text-right border-l border-stroke dark:border-strokedark pl-6 md:pl-8">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">FACTURA DE VENTA</p>
                    <p class="text-3xl font-bold text-black dark:text-white font-mono leading-none">{{ $billData['number'] ?? $factura->numero }}</p>
                    <p class="text-sm text-success font-medium mt-2">{{ $billData['validated'] ?? '' }}</p>
                </div>
            </div>

            <div class="border-b border-stroke dark:border-strokedark my-8"></div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- COLUMNA PRINCIPAL IZQUIERDA --}}
            <div class="col-span-1 lg:col-span-2 space-y-8">

                {{-- Detalles Fiscales y Totales Básicos --}}
                @if($billData)
                <div>
                    <h3 class="font-medium text-black dark:text-white mb-6">Detalles de Emisión</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Cód. Referencia</p>
                            <p class="font-semibold text-black dark:text-white">{{ $billData['reference_code'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Forma de Pago</p>
                            <p class="font-semibold text-black dark:text-white">{{ $billData['payment_form']['name'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Método de Pago</p>
                            <p class="font-semibold text-black dark:text-white">{{ $billData['payment_method']['name'] ?? '—' }}</p>
                        </div>

                        @if(!empty($billData['gross_value']))
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Valor Bruto</p>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">${{ number_format((float)$billData['gross_value'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Consumo / Descuentos</p>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">Desc: ${{ number_format((float)($billData['discount_amount'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">IVA Total</p>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">${{ number_format((float)($billData['tax_amount'] ?? 0), 2) }}</p>
                        </div>
                        @else
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Total</p>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">${{ number_format((float)$factura->total, 2) }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="border-b border-stroke dark:border-strokedark my-6"></div>
                @endif

                {{-- Productos Facturados --}}
                <div>
                    <h3 class="font-medium text-black dark:text-white mb-6">Detalle de Productos</h3>

                    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Concepto</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cant.</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Valor Un.</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IVA%</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Desc</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @if(count($itemsData) > 0)
                                    @foreach($itemsData as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-black dark:text-white">{{ $item['name'] }}</p>
                                            <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $item['code_reference'] }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-center text-black dark:text-white font-medium">{{ $item['quantity'] }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">${{ number_format((float)$item['price'], 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold px-2 py-0.5">{{ $item['tax_rate'] }}%</span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">${{ number_format((float)($item['discount'] ?? 0), 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-black dark:text-white">${{ number_format((float)$item['total'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    {{-- Ver local data if no Factus item details --}}
                                    @foreach(($factura->json_request['items'] ?? []) as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                        <td class="px-4 py-3 font-medium text-black dark:text-white">{{ $item['name'] }}</td>
                                        <td class="px-4 py-3 text-center">{{ $item['quantity'] }}</td>
                                        <td class="px-4 py-3 text-right">${{ number_format($item['price'], 2) }}</td>
                                        <td class="px-4 py-3 text-center">-</td>
                                        <td class="px-4 py-3 text-center">-</td>
                                        <td class="px-4 py-3 text-right font-bold text-black dark:text-white">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl">
                        <span class="font-bold text-gray-700 dark:text-gray-300 mr-4">Total Factura:</span>
                        <span class="text-2xl font-black text-primary">${{ number_format((float)($billData['total'] ?? $factura->total), 2) }}</span>
                    </div>

                </div>

            </div>

            {{-- COLUMNA LATERAL DERECHA (Cliente, CUFE, QR) --}}
            <div class="col-span-1 border-l-0 lg:border-l border-stroke dark:border-strokedark lg:pl-10 space-y-8">

                {{-- Cliente --}}
                @if($customerData)
                <div>
                    <h3 class="font-medium text-black dark:text-white mb-4">Receptor / Cliente</h3>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Nombre ó Razón Social</p>
                            <p class="font-semibold text-black dark:text-white">{{ $customerData['names'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Identificación</p>
                            <p class="font-semibold text-gray-700 dark:text-gray-300 font-mono">{{ $customerData['identification'] ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Contacto</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $customerData['email'] ?? 'Sin Email' }}</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $customerData['phone'] ?? 'Sin Teléfono' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Ubicación</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $customerData['municipality']['name'] ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-stroke dark:border-strokedark my-6"></div>
                @endif

                {{-- CUFE --}}
                <div>
                    <h3 class="font-medium text-black dark:text-white mb-4">Código CUFE</h3>
                    <div class="rounded-xl border border-stroke dark:border-strokedark bg-gray-50 dark:bg-gray-800 p-3 select-all cursor-pointer">
                        <p class="text-xs font-mono text-gray-600 dark:text-gray-400 break-all leading-relaxed">
                            {{ $factura->cufe ?? 'No asignado o en proceso' }}
                        </p>
                    </div>
                </div>

                <div class="border-b border-stroke dark:border-strokedark my-6"></div>

                {{-- Resolución --}}
                @if($rangeData)
                <div>
                    <h3 class="font-medium text-black dark:text-white mb-4">Resolución DIAN</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center border-b border-stroke dark:border-strokedark pb-2">
                            <span class="text-gray-500">Prefijo</span>
                            <span class="font-bold text-black dark:text-white font-mono">{{ $rangeData['prefix'] ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-stroke dark:border-strokedark pb-2">
                            <span class="text-gray-500">N° Res.</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $rangeData['resolution_number'] ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-stroke dark:border-strokedark pb-2">
                            <span class="text-gray-500">Rango</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ number_format($rangeData['from'] ?? 0) }} / {{ number_format($rangeData['to'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Vence</span>
                            <span class="text-gray-700 dark:text-gray-300 text-xs">{{ $rangeData['end_date'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- QR --}}
                @if($qrImage)
                <div class="border-b border-stroke dark:border-strokedark my-6"></div>
                <div class="text-center">
                    <h3 class="font-medium text-black dark:text-white mb-4">Código QR DIAN</h3>
                    <img src="{{ $qrImage }}" alt="QR DIAN" class="mx-auto rounded-lg shadow-sm border border-gray-100 p-2 bg-white max-w-[150px]">
                    <p class="text-xs text-gray-500 mt-4">Escanea para validar en el portal.</p>
                </div>
                @endif

            </div>
        </div>

    </div>

@endsection
