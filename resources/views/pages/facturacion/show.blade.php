@extends('layouts.app')
@php
    $title = 'Detalle de Factura DIAN';
@endphp

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────── --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex flex-wrap items-center gap-3">
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            Factura DIAN:&nbsp;<span class="text-primary">{{ $factura->numero }}</span>
        </h2>
        @if($factura->status === 'Validado')
            <span class="inline-flex items-center gap-1.5 rounded-md bg-success bg-opacity-10 px-3 py-1 text-xs font-semibold text-success">
                <span class="h-2 w-2 rounded-full bg-success"></span> Validado DIAN
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-md bg-warning bg-opacity-10 px-3 py-1 text-xs font-semibold text-warning">
                <span class="h-2 w-2 rounded-full bg-warning"></span> {{ $factura->status }}
            </span>
        @endif
    </div>
    <nav>
        <ol class="flex items-center gap-2 text-sm">
            <li><a class="font-medium hover:text-primary transition-colors" href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a class="font-medium hover:text-primary transition-colors" href="{{ route('facturacion.index') }}">Facturación</a></li>
            <li class="text-gray-400">/</li>
            <li class="font-medium text-primary">Detalle</li>
        </ol>
    </nav>
</div>

{{-- ── Barra de acciones ───────────────────────────────────── --}}
<div class="mb-6 flex flex-wrap gap-3">
    <a href="{{ route('facturacion.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver
    </a>

    @if($factura->numero && $factura->numero !== 'N/A')
        <a href="{{ route('facturacion.pdf', $factura->id) }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl border border-primary bg-primary bg-opacity-10 px-4 py-2.5 text-sm font-medium text-primary transition hover:bg-opacity-20">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Descargar PDF
        </a>
    @endif

    @if($factura->qr)
        <a href="{{ $factura->qr }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Ver en DIAN
        </a>
    @endif

    @if($factura->numero && $factura->numero !== 'N/A')
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Enviar por Correo
            </button>
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute left-0 top-full mt-2 w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-xl dark:border-gray-700 dark:bg-gray-800"
                 style="z-index: 50;">
                <form action="{{ route('facturacion.sendEmail', $factura->id) }}" method="POST">
                    @csrf
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Correo del Cliente
                    </label>
                    <input type="email" name="email" required placeholder="correo@empresa.com"
                           value="{{ $factura->venta->cliente->email ?? '' }}"
                           class="mb-3 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-black outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 dark:text-white">
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="open = false"
                                class="rounded-lg px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Cancelar</button>
                        <button type="submit"
                                class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-opacity-90 transition">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@php
    $resp         = $factura->json_response ?? [];
    $billData     = $resp['data']['bill']            ?? null;
    $customerData = $resp['data']['customer']        ?? null;
    $companyData  = $resp['data']['company']         ?? null;
    $itemsData    = $resp['data']['items']           ?? [];
    $rangeData    = $resp['data']['numbering_range'] ?? null;
    $qrImage      = $resp['data']['bill']['qr_image'] ?? null;
@endphp

{{-- ── Contenedor principal ───────────────────────────────── --}}
<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900"
     style="padding: 1.75rem; overflow: hidden;">

    {{-- ── Cabecera Emisor ──────────────────────────────── --}}
    @if($companyData)
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            @if(!empty($companyData['url_logo']))
                <img src="{{ $companyData['url_logo'] }}" alt="Logo"
                     style="height: 60px; width: auto; object-fit: contain; background: white; border-radius: 0.5rem; padding: 4px;">
            @endif
            <div>
                <p style="font-size: 1.125rem; font-weight: 700;" class="text-black dark:text-white">
                    {{ $companyData['company'] ?? 'Empresa' }}
                </p>
                <p style="font-size: 0.75rem; margin-top: 2px;" class="text-gray-500 font-mono">
                    NIT: {{ $companyData['nit'] ?? '' }}-{{ $companyData['dv'] ?? '' }}
                </p>
                <p style="font-size: 0.75rem;" class="text-gray-500">
                    {{ $companyData['direction'] ?? '' }}{{ isset($companyData['municipality']) ? ' — ' . $companyData['municipality'] : '' }}
                </p>
                <p style="font-size: 0.75rem;" class="text-gray-500">
                    {{ $companyData['phone'] ?? '' }}{{ isset($companyData['email']) ? ' · ' . $companyData['email'] : '' }}
                </p>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;" class="text-gray-400">FACTURA ELECTRÓNICA DE VENTA</p>
            <p style="font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 4px;" class="text-black dark:text-white font-mono">
                {{ $billData['number'] ?? $factura->numero }}
            </p>
            @if(!empty($billData['validated']))
                <p style="font-size: 0.8rem; margin-top: 6px; font-weight: 500;" class="text-success">
                    {{ $billData['validated'] }}
                </p>
            @endif
        </div>
    </div>
    <hr class="border-gray-100 dark:border-gray-700 mb-6">
    @endif

    {{-- ── Grid principal: 2/3 izquierda · 1/3 derecha ── --}}
    {{-- Usamos style para garantizar comportamiento en producción --}}
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;"
         class="factura-grid">

        {{-- Columna izquierda –– 2/3 --}}
        <div style="min-width: 0; overflow: hidden;">

            {{-- Detalles de Emisión --}}
            @if($billData)
            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;" class="text-black dark:text-white">
                    Detalles de Emisión
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">Cód. Referencia</p>
                        <p style="font-size: 0.875rem; font-weight: 600; word-break: break-all;" class="text-black dark:text-white">
                            {{ $billData['reference_code'] ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">Forma de Pago</p>
                        <p style="font-size: 0.875rem; font-weight: 600;" class="text-black dark:text-white">
                            {{ $billData['payment_form']['name'] ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">Método de Pago</p>
                        <p style="font-size: 0.875rem; font-weight: 600;" class="text-black dark:text-white">
                            {{ $billData['payment_method']['name'] ?? '—' }}
                        </p>
                    </div>
                    @if(!empty($billData['gross_value']))
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">Valor Bruto</p>
                        <p style="font-size: 0.875rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">
                            ${{ number_format((float)$billData['gross_value'], 2) }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">Descuento</p>
                        <p style="font-size: 0.875rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">
                            ${{ number_format((float)($billData['discount_amount'] ?? 0), 2) }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">IVA Total</p>
                        <p style="font-size: 0.875rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">
                            ${{ number_format((float)($billData['tax_amount'] ?? 0), 2) }}
                        </p>
                    </div>
                    @else
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px;" class="text-gray-500">Total</p>
                        <p style="font-size: 0.875rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">
                            ${{ number_format((float)$factura->total, 2) }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            <hr class="border-gray-100 dark:border-gray-700 mb-6">
            @endif

            {{-- Tabla de Productos --}}
            <div>
                <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;" class="text-black dark:text-white">
                    Detalle de Productos Facturados
                </p>
                <div style="overflow-x: auto; border-radius: 0.75rem; border: 1px solid; border-color: #e5e7eb;"
                     class="dark:border-gray-700">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;" class="bg-gray-50 dark:bg-white/[0.03] dark:border-gray-700">
                                <th style="padding: 10px 14px; text-align: left; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;" class="text-gray-500">Concepto</th>
                                <th style="padding: 10px 14px; text-align: center; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;" class="text-gray-500">Cant.</th>
                                <th style="padding: 10px 14px; text-align: right; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;" class="text-gray-500">Valor Un.</th>
                                <th style="padding: 10px 14px; text-align: center; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;" class="text-gray-500">IVA%</th>
                                <th style="padding: 10px 14px; text-align: center; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;" class="text-gray-500">Desc.</th>
                                <th style="padding: 10px 14px; text-align: right; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;" class="text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($itemsData) > 0)
                                @foreach($itemsData as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;" class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td style="padding: 10px 14px;">
                                        <p style="font-weight: 600;" class="text-black dark:text-white">{{ $item['name'] }}</p>
                                        <p style="font-size: 0.7rem; margin-top: 2px; font-family: monospace;" class="text-gray-400">{{ $item['code_reference'] }}</p>
                                    </td>
                                    <td style="padding: 10px 14px; text-align: center; font-weight: 600;" class="text-black dark:text-white">{{ $item['quantity'] }}</td>
                                    <td style="padding: 10px 14px; text-align: right; white-space: nowrap;" class="text-gray-600 dark:text-gray-400">
                                        ${{ number_format((float)$item['price'], 2) }}
                                    </td>
                                    <td style="padding: 10px 14px; text-align: center;">
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background: rgba(59,130,246,0.1); color: #3b82f6;">
                                            {{ $item['tax_rate'] }}%
                                        </span>
                                    </td>
                                    <td style="padding: 10px 14px; text-align: center; white-space: nowrap;" class="text-gray-600 dark:text-gray-400">
                                        ${{ number_format((float)($item['discount'] ?? 0), 2) }}
                                    </td>
                                    <td style="padding: 10px 14px; text-align: right; font-weight: 700; white-space: nowrap;" class="text-black dark:text-white">
                                        ${{ number_format((float)($item['total'] ?? ($item['price'] * $item['quantity'])), 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                @foreach(($factura->json_request['items'] ?? []) as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;" class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td style="padding: 10px 14px; font-weight: 600;" class="text-black dark:text-white">{{ $item['name'] }}</td>
                                    <td style="padding: 10px 14px; text-align: center;" class="text-black dark:text-white">{{ $item['quantity'] }}</td>
                                    <td style="padding: 10px 14px; text-align: right; white-space: nowrap;" class="text-gray-600 dark:text-gray-400">
                                        ${{ number_format((float)$item['price'], 2) }}
                                    </td>
                                    <td style="padding: 10px 14px; text-align: center;">—</td>
                                    <td style="padding: 10px 14px; text-align: center;">—</td>
                                    <td style="padding: 10px 14px; text-align: right; font-weight: 700; white-space: nowrap;" class="text-black dark:text-white">
                                        ${{ number_format((float)($item['price'] * $item['quantity']), 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Total final --}}
                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 1rem; margin-top: 1rem; padding: 1rem 1.25rem; border-radius: 0.75rem; background: rgba(0,0,0,0.03);" class="dark:bg-gray-800/50">
                    <span style="font-weight: 600; font-size: 0.9rem;" class="text-gray-600 dark:text-gray-300">Total Factura:</span>
                    <span style="font-size: 1.5rem; font-weight: 900;" class="text-primary">
                        ${{ number_format((float)($billData['total'] ?? $factura->total), 2) }}
                    </span>
                </div>
            </div>

        </div>{{-- /col-izquierda --}}

        {{-- Columna derecha –– 1/3 --}}
        <div style="min-width: 0; overflow: hidden;" class="factura-sidebar">

            {{-- Separador mobile --}}
            <hr class="border-gray-100 dark:border-gray-700 mb-6 factura-hr-mobile">

            {{-- Cliente --}}
            @if($customerData)
            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;" class="text-black dark:text-white">Receptor / Cliente</p>
                <div style="display: flex; flex-direction: column; gap: 0.875rem; font-size: 0.85rem;">
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 3px;" class="text-gray-500">Nombre / Razón Social</p>
                        <p style="font-weight: 600;" class="text-black dark:text-white">{{ $customerData['names'] ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 3px;" class="text-gray-500">Identificación</p>
                        <p style="font-weight: 600; font-family: monospace;" class="text-gray-700 dark:text-gray-300">{{ $customerData['identification'] ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 3px;" class="text-gray-500">Contacto</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $customerData['email'] ?? 'Sin email' }}</p>
                        <p style="margin-top: 2px;" class="text-gray-700 dark:text-gray-300">{{ $customerData['phone'] ?? 'Sin teléfono' }}</p>
                    </div>
                    @if(!empty($customerData['municipality']['name']))
                    <div>
                        <p style="font-size: 0.7rem; font-weight: 600; margin-bottom: 3px;" class="text-gray-500">Municipio</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $customerData['municipality']['name'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <hr class="border-gray-100 dark:border-gray-700 mb-6">
            @endif

            {{-- CUFE (fix: break-all con contenedor controlado) --}}
            <div style="margin-bottom: 1.5rem;" x-data="{ copied: false }">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <p style="font-size: 0.8rem; font-weight: 600;" class="text-black dark:text-white">Código CUFE</p>
                    @if($factura->cufe)
                    <button @click="navigator.clipboard.writeText('{{ $factura->cufe }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="text-xs text-gray-400 hover:text-primary transition-colors flex items-center gap-1">
                        <svg x-show="!copied" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <svg x-show="copied" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;"><polyline points="20 6 9 17 4 12"/></svg>
                        <span x-text="copied ? '¡Copiado!' : 'Copiar'"></span>
                    </button>
                    @endif
                </div>
                <div style="border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 0.75rem; background: rgba(0,0,0,0.02); overflow: hidden;"
                     class="dark:border-gray-700 dark:bg-gray-800/50">
                    <p style="font-size: 0.72rem; font-family: monospace; line-height: 1.6; word-break: break-all; overflow-wrap: break-word; white-space: normal; max-width: 100%;"
                       class="text-gray-600 dark:text-gray-400">
                        {{ $factura->cufe ?? 'No asignado aún' }}
                    </p>
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-700 mb-6">

            {{-- Resolución DIAN --}}
            @if($rangeData)
            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;" class="text-black dark:text-white">Resolución DIAN</p>
                <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0;">
                    @foreach([
                        'Prefijo'   => $rangeData['prefix'] ?? '—',
                        'N° Res.'   => $rangeData['resolution_number'] ?? '—',
                        'Rango'     => number_format($rangeData['from'] ?? 0) . ' / ' . number_format($rangeData['to'] ?? 0),
                        'Vence'     => $rangeData['end_date'] ?? '—',
                    ] as $label => $value)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;" class="dark:border-gray-700">
                        <span class="text-gray-500">{{ $label }}</span>
                        <span style="font-weight: 600; font-family: monospace; font-size: 0.8rem; text-align: right;" class="text-black dark:text-white">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <hr class="border-gray-100 dark:border-gray-700 mb-6">
            @endif

            {{-- QR DIAN --}}
            @if($qrImage)
            <div style="text-align: center;">
                <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;" class="text-black dark:text-white">Código QR DIAN</p>
                <img src="{{ $qrImage }}" alt="QR DIAN"
                     style="max-width: 160px; margin: 0 auto; display: block; padding: 8px; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                <p style="font-size: 0.72rem; margin-top: 0.75rem;" class="text-gray-400">Escanea para validar en el portal DIAN.</p>
            </div>
            @endif

        </div>{{-- /col-derecha --}}

    </div>{{-- /grid --}}

</div>

{{-- ── CSS para el grid responsivo (no depende de purge de Tailwind) ──── --}}
<style>
    @media (min-width: 1024px) {
        .factura-grid {
            grid-template-columns: 2fr 1fr !important;
        }
        .factura-sidebar {
            border-left: 1px solid #e5e7eb;
            padding-left: 2rem;
        }
        .dark .factura-sidebar {
            border-left-color: rgba(255,255,255,0.06);
        }
        .factura-hr-mobile {
            display: none !important;
        }
    }
</style>

@endsection
