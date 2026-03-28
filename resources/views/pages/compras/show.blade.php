@extends('layouts.app')
@php
    $title = 'Detalles de Compra';
@endphp

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            Compra #{{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}
            <span
                class="inline-flex rounded-full bg-{{ $compra->estado_badge_color }}-100 py-1.5 px-3 text-sm font-medium text-{{ $compra->estado_badge_color }}-600 capitalize ml-4">
                {{ $compra->estado }}
            </span>
        </h2>

        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ route('dashboard') }}">Dashboard /</a>
                </li>
                <li>
                    <a class="font-medium" href="{{ route('compras.index') }}">Compras /</a>
                </li>
                <li class="font-medium text-primary">Detalle</li>
            </ol>
        </nav>
    </div>

    @if (session('sweet_alert'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{{ session('sweet_alert.type') }}',
                    title: '{{ session('sweet_alert.title') }}',
                    text: '{!! session('sweet_alert.message') !!}',
                    confirmButtonColor: '#3C50E0',
                });
            });
        </script>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3 mb-6">
        <!-- Info Principal -->
        <div
            class="col-span-1 xl:col-span-2 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6">
            <h3 class="font-medium text-black dark:text-white mb-4">Información del Documento</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                <div>
                    <span class="text-sm font-medium text-body-space mb-1 block">Proveedor</span>
                    <p class="font-semibold text-black dark:text-white">{{ $compra->proveedor->empresa }}</p>
                    <p class="text-xs text-body-space">{{ $compra->proveedor->ruc ?? $compra->proveedor->email }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-body-space mb-1 block">Nº Factura</span>
                    <p class="font-semibold text-black dark:text-white">{{ $compra->numero_factura }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-body-space mb-1 block">Fecha de Compra</span>
                    <p class="font-semibold text-black dark:text-white">{{ $compra->fecha_compra->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-body-space mb-1 block">Sucursal</span>
                    <p class="font-semibold text-black dark:text-white">{{ $compra->sucursal->nombre }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-body-space mb-1 block">Registrado por</span>
                    <p class="font-semibold text-black dark:text-white">{{ $compra->usuario->name }}</p>
                </div>
            </div>
        </div>

        <!-- Condiciones de Pago -->
        <div
            class="col-span-1 border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6 rounded-sm">
            <h3 class="font-medium text-black dark:text-white mb-4">Condiciones</h3>

            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center py-2 border-b border-stroke dark:border-strokedark">
                    <span class="text-sm font-medium text-body-space block">Tipo:</span>
                    <span
                        class="inline-flex rounded-full bg-{{ $compra->tipo_compra_badge_color }}-100 py-1 px-3 text-xs font-medium text-{{ $compra->tipo_compra_badge_color }}-600 uppercase">
                        {{ $compra->tipo_compra }}
                    </span>
                </div>

                @if ($compra->tipo_compra === 'credito')
                    <div class="flex justify-between items-center py-2 border-b border-stroke dark:border-strokedark">
                        <span class="text-sm font-medium text-body-space block">Vencimiento:</span>
                        <span
                            class="font-semibold text-black dark:text-white">{{ $compra->fecha_vencimiento ? $compra->fecha_vencimiento->format('d/m/Y') : '-' }}</span>
                    </div>
                @endif

                @if ($compra->metodo_pago)
                    <div class="flex justify-between items-center py-2 border-b border-stroke dark:border-strokedark">
                        <span class="text-sm font-medium text-body-space block">Método de Pago:</span>
                        <span class="font-semibold text-black dark:text-white">{{ $compra->metodo_pago }}</span>
                    </div>
                @endif
            </div>

            <!-- Acciones -->
            <div class="mt-6 flex flex-col gap-3">
                <a href="{{ route('compras.pdf', $compra->id) }}" target="_blank"
                    class="w-full flex items-center justify-center gap-2 rounded border border-danger text-danger py-2 px-4 font-medium hover:bg-danger hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 16L7 11L8.4 9.55L11 12.15V4H13V12.15L15.6 9.55L17 11L12 16ZM6 20C5.45 20 4.97917 19.8042 4.5875 19.4125C4.19583 19.0208 4 18.55 4 18V15H6V18H18V15H20V18C20 18.55 19.8042 19.0208 19.4125 19.4125C19.0208 19.8042 18.55 20 18 20H6Z" />
                    </svg>
                    Descargar Factura PDF
                </a>

                @if ($compra->estado === 'registrada')
                    <button type="button" onclick="confirmarAnulacion()"
                        class="w-full flex items-center justify-center gap-2 rounded border border-danger text-danger py-2 px-4 font-medium hover:bg-danger hover:text-red-600 transition-colors">
                        Anular Compra
                    </button>
                    <!-- Form oculto -->
                    <form id="form-anular" action="{{ route('compras.anular', $compra->id) }}" method="POST"
                        style="display: none;">
                        @csrf
                        <input type="hidden" name="motivo_anulacion" id="input_motivo">
                    </form>

                    <script>
                        function confirmarAnulacion() {
                            Swal.fire({
                                title: '¿Anular esta compra?',
                                text: "El stock de los productos regresará a su estado anterior. ¡Esta acción no se puede deshacer!",
                                icon: 'warning',
                                input: 'text',
                                inputLabel: 'Motivo de la anulación (obligatorio)',
                                inputPlaceholder: 'Ej. Factura duplicada, mal ingresada...',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Sí, anular compra',
                                cancelButtonText: 'Cancelar',
                                preConfirm: (motivo) => {
                                    if (!motivo) {
                                        Swal.showValidationMessage('Debe ingresar un motivo para la anulación')
                                    }
                                    return motivo
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('input_motivo').value = result.value;
                                    document.getElementById('form-anular').submit();
                                }
                            })
                        }
                    </script>
                @endif
            </div>
        </div>
    </div>

    <!-- Detalle de Productos -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="px-6 py-4 flex justify-between items-center border-b border-stroke dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">Productos Adquiridos</h3>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="min-w-[50px] py-4 px-6 font-medium text-black dark:text-white">#</th>
                        <th class="min-w-[200px] py-4 px-6 font-medium text-black dark:text-white">Producto</th>
                        <th class="min-w-[100px] py-4 px-6 font-medium text-black dark:text-white text-right">Cantidad</th>
                        <th class="min-w-[150px] py-4 px-6 font-medium text-black dark:text-white text-right">Costo Unit.
                        </th>
                        <th class="min-w-[150px] py-4 px-6 font-medium text-black dark:text-white text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compra->detalles as $index => $detalle)
                        <tr>
                            <td class="border-b border-[#eee] py-4 px-6 dark:border-strokedark text-black dark:text-white">
                                {{ $index + 1 }}
                            </td>
                            <td class="border-b border-[#eee] py-4 px-6 dark:border-strokedark">
                                <span
                                    class="font-medium text-black dark:text-white">{{ $detalle->producto->nombre }}</span>
                                <span class="block text-xs text-body-space">SKU:
                                    {{ $detalle->producto->sku ?? 'N/A' }}</span>
                            </td>
                            <td
                                class="border-b border-[#eee] py-4 px-6 dark:border-strokedark text-right text-black dark:text-white">
                                {{ $detalle->cantidad }}
                            </td>
                            <td
                                class="border-b border-[#eee] py-4 px-6 dark:border-strokedark text-right text-black dark:text-white">
                                ${{ number_format($detalle->precio_compra, 2) }}
                            </td>
                            <td class="border-b border-[#eee] py-4 px-6 dark:border-strokedark text-right">
                                <span
                                    class="font-bold text-black dark:text-white">${{ number_format($detalle->subtotal, 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 flex justify-end">
            <div class="w-full sm:w-1/2 md:w-1/3">
                <div class="flex justify-between py-3 border-t border-stroke dark:border-strokedark">
                    <span class="font-medium text-black dark:text-white text-lg">Total Compra:</span>
                    <span class="font-bold text-xl text-primary">${{ number_format($compra->total, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    @if ($compra->observaciones)
        <div
            class="mt-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6">
            <h3 class="font-medium text-black dark:text-white mb-2">Observaciones / Notas</h3>
            <p class="text-body-space whitespace-pre-wrap">{{ $compra->observaciones }}</p>
        </div>
    @endif
@endsection
