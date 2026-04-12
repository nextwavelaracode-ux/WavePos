@extends('layouts.app')

@section('title', 'Facturas Electrónicas DIAN')

@section('content')
<div class="p-4 md:p-6 mx-auto w-full max-w-screen-2xl">
    
    {{-- Breadcrumbs y Titulo Superior --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Hogar
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <a href="#" class="ml-1 text-sm font-medium text-gray-500 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">Comercio electrónico</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-400 md:ml-2 dark:text-gray-500">Facturas</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Mis facturas</h1>
    </div>

    {{-- Cards KPI --}}
    <div class="mb-6 grid md:grid-cols-3 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">

        {{-- Validado DIAN --}}
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Validado DIAN</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($stats['validado_monto'], 0) }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">{{ $stats['validado_count'] }} facturas</p>
                </div>
            </div>
        </div>

        {{-- Pendiente / Error --}}
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Pendiente / Error</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($stats['pendiente_monto'], 0) }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">{{ $stats['pendiente_count'] }} facturas</p>
                </div>
            </div>
        </div>

        {{-- Total Global --}}
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Total Global</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($stats['validado_monto'] + $stats['pendiente_monto'], 0) }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">{{ $stats['validado_count'] + $stats['pendiente_count'] }} facturas</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Toolbar Búsqueda y Botones Activos --}}
    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
        <div class="w-full md:w-1/2 flex items-center space-x-2">
            <form class="flex items-center w-full max-w-sm">
                <label for="simple-search" class="sr-only">Buscar factura</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="simple-search" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 p-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500 outline-none" placeholder="Buscar factura">
                </div>
            </form>
            <button type="button" class="flex items-center justify-center text-white bg-brand-600 hover:bg-brand-700 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-brand-600 dark:hover:bg-brand-700 transition">
                Buscar
            </button>
            <button type="button" class="flex items-center justify-center bg-white border border-gray-300 text-gray-500 font-medium rounded-lg text-sm px-4 py-2.5 hover:bg-gray-50 hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400 dark:hover:bg-neutral-700 dark:hover:text-white dark:focus:ring-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filtrar
                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <button type="button" class="hidden sm:flex items-center justify-center bg-white border border-gray-300 text-gray-500 font-medium rounded-lg text-sm px-4 py-2.5 hover:bg-gray-50 hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400 dark:hover:bg-neutral-700 dark:hover:text-white dark:focus:ring-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Configuraciones
                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>
        <div class="w-full md:w-auto flex justify-end">
            <a href="{{ route('facturacion.create') }}" class="flex items-center justify-center text-white bg-brand-600 hover:bg-brand-700 font-medium rounded-lg text-sm px-4 py-2.5 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Crear una factura
            </a>
        </div>
    </div>

    {{-- Radio Buttons Mostrar Solo --}}
    <div class="flex items-center gap-4 mb-4 text-sm text-gray-500 dark:text-gray-400">
        <span class="font-bold text-gray-900 dark:text-white">Mostrar solo:</span>
        <label class="flex items-center gap-1.5 cursor-pointer hover:text-gray-900 dark:hover:text-white">
            <input type="radio" name="mostrar" class="w-4 h-4 text-brand-600 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600" checked> Todo
        </label>
        <label class="flex items-center gap-1.5 cursor-pointer hover:text-gray-900 dark:hover:text-white">
            <input type="radio" name="mostrar" class="w-4 h-4 text-brand-600 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600"> No validado
        </label>
        <label class="flex items-center gap-1.5 cursor-pointer hover:text-gray-900 dark:hover:text-white">
            <input type="radio" name="mostrar" class="w-4 h-4 text-brand-600 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600"> Validado
        </label>
    </div>

    {{-- Tabla Flowbite --}}
    <div class="bg-white dark:bg-neutral-800 relative shadow-sm border border-gray-100 dark:border-neutral-700 overflow-hidden text-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3 w-10">
                            <div class="flex items-center">
                                <input type="checkbox" class="w-4 h-4 text-brand-600 bg-gray-100 border-gray-300 rounded focus:ring-brand-500 dark:focus:ring-brand-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 font-semibold uppercase tracking-widest text-[10px]">ID de Factura</th>
                        <th scope="col" class="px-4 py-3 font-semibold uppercase tracking-widest text-[10px] flex items-center">Cliente <svg class="w-3 h-3 ml-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 15V9m4 6V9m4 6V9m-8 6h8m-8-6h8"/></svg></th>
                        <th scope="col" class="px-4 py-3 font-semibold uppercase tracking-widest text-[10px]">Correo Electrónico</th>
                        <th scope="col" class="px-4 py-3 font-semibold uppercase tracking-widest text-[10px] flex items-center">Fecha de Creación <svg class="w-3 h-3 ml-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 15V9m4 6V9m4 6V9m-8 6h8m-8-6h8"/></svg></th>
                        <th scope="col" class="px-4 py-3 font-semibold uppercase tracking-widest text-[10px]">Cantidad</th>
                        <th scope="col" class="px-4 py-3 font-semibold uppercase tracking-widest text-[10px] flex items-center">Estado <svg class="w-3 h-3 ml-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 15V9m4 6V9m4 6V9m-8 6h8m-8-6h8"/></svg></th>
                        <th scope="col" class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-700">
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition bg-white dark:bg-neutral-800">
                        <td class="w-4 px-4 py-3">
                            <div class="flex items-center">
                                <input type="checkbox" class="w-4 h-4 text-brand-600 bg-gray-100 border-gray-300 rounded focus:ring-brand-500 dark:focus:ring-brand-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">
                            #{{ $factura->numero !== 'N/A' ? $factura->numero : 'FW-' . $factura->id }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-medium">
                            {{ $factura->venta->cliente->nombre ?? 'Consumidor Final' }} {{ $factura->venta->cliente->apellido ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $factura->venta->cliente->email ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $factura->created_at->translatedFormat('d \d\e F \d\e Y') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($factura->total, 0) }}
                        </td>
                        <td class="px-4 py-3">
                            @if($factura->status === 'Validado')
                            <span class="inline-flex items-center bg-emerald-100 text-emerald-800 text-xs font-semibold px-2 py-0.5 rounded border border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-400 dark:border-emerald-800">Validado</span>
                            @else
                            <span class="inline-flex items-center bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded border border-red-200 dark:bg-red-900/40 dark:text-red-400 dark:border-red-800">{{ $factura->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center gap-2" x-data="{ openOptions: false }">
                                <a href="{{ route('facturacion.show', $factura->id) }}" class="inline-flex items-center justify-center rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-neutral-700 text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button type="button" class="inline-flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15"><path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                            No se encontraron facturas. Activa la opción tributaria al cobrar ventas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($facturas->hasPages())
        <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-t border-gray-200 dark:border-neutral-700" aria-label="Table navigation">
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                Mostrando <span class="font-semibold text-gray-900 dark:text-white">{{ $facturas->firstItem() }}-{{ $facturas->lastItem() }}</span> de <span class="font-semibold text-gray-900 dark:text-white">{{ $facturas->total() }}</span>
            </span>
            <div class="inline-flex items-stretch -space-x-px">
                {{ $facturas->links('pagination::tailwind') }}
            </div>
        </nav>
        @endif
    </div>

</div>
@endsection
