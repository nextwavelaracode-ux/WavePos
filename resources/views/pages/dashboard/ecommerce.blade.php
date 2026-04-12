@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- ============================================================
     DASHBOARD – Estilo HopeUI con TailwindCSS puro
     ============================================================ --}}

{{-- 1. Hero Banner --}}
<x-dashboard.hero-banner />

{{-- 2. KPI Cards (flotan sobre el banner) --}}
<x-dashboard.kpi-cards
    :totalVentas="$totalVentas ?? 0"
    :totalGanancia="$totalGanancia ?? 0"
    :totalCostos="$totalCostos ?? 0"
    :totalIngresos="$totalIngresos ?? 0"
    :ingresosNetos="$ingresosNetos ?? 0"
    :ventasHoy="$ventasHoy ?? 0"
    :totalClientes="$totalClientes ?? 0"
/>

{{-- 3. Cuerpo principal: 2 columnas --}}
<div class="flex flex-col xl:flex-row gap-5">

    {{-- ── Columna Izquierda (2/3) ──────────────────────────── --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- 3a. Gráfica de ventas brutas --}}
        <x-dashboard.gross-sales-chart :ventasMensuales="$ventasMensuales ?? []" />

        {{-- 3b. Ingresos + Conversiones side-by-side --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-dashboard.earnings-chart :ventasCategoria="$ventasCategoria ?? []" />
            <x-dashboard.conversions-chart :ventasDiarias="$ventasDiarias ?? []" />
        </div>

        {{-- 3c. Tabla de clientes --}}
        <x-dashboard.clients-table :clientes="$topClientes ?? []" />

    </div>

    {{-- ── Columna Derecha (1/3) ────────────────────────────── --}}
    <div class="w-full xl:w-80 flex flex-col gap-5">

        {{-- 3d. Panel de ventas (stats + botones + visitantes) --}}
        <x-dashboard.sales-panel
            :totalProductos="$totalProductos ?? 0"
            :totalOrdenes="$totalOrdenes ?? 0"
            :ventasLifetime="$ventasLifetime ?? 0"
            :visitantes="$totalVentas ?? 0"
            :clientesNuevos="$clientesNuevos ?? 0"
        />

        {{-- 3e. Activity Overview --}}
        <x-dashboard.activity-overview :actividades="$actividades ?? []" />

    </div>
</div>

@endsection
