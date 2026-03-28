@extends('layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-screen-2xl md:p-6">

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Devoluciones</h2>
            <p class="text-sm text-gray-500">Registro de devoluciones de clientes</p>
        </div>
        <button onclick="document.getElementById('modalDevolucion').classList.remove('hidden')"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition">
            + Registrar Devolución
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    {{-- Tabla --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Venta</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Producto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Motivo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($devoluciones as $dev)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $dev->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-brand-600 dark:text-brand-400">{{ $dev->venta?->numero ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $dev->producto?->nombre ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $dev->cantidad }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $dev->motivo }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $dev->usuario?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">No hay devoluciones registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($devoluciones->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-gray-800">{{ $devoluciones->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal devolución --}}
<div id="modalDevolucion" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl" @click.stop>
        <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Registrar Devolución</h3>
            <button onclick="document.getElementById('modalDevolucion').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('caja.devoluciones.store') }}" method="POST" class="p-5 space-y-4">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Venta Asociada <span class="text-red-500">*</span></label>
                <select name="venta_id" id="ventaSelect" onchange="cargarProductosVenta()" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="">Seleccionar venta...</option>
                    @foreach($ventas as $v)
                        <option value="{{ $v->id }}" data-productos="{{ json_encode($v->detalles->map(fn($d) => ['id'=>$d->producto_id,'nombre'=>$d->producto?->nombre,'cantidad'=>$d->cantidad])) }}">
                            {{ $v->numero }} — {{ $v->fecha->format('d/m/Y') }} ({{ $v->cliente?->nombre_completo ?? 'Consumidor Final' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Producto <span class="text-red-500">*</span></label>
                <select name="producto_id" id="productoSelect" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="">— Selecciona venta primero —</option>
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cantidad <span class="text-red-500">*</span></label>
                <input type="number" name="cantidad" min="1" required
                       class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                       placeholder="1">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Motivo <span class="text-red-500">*</span></label>
                <textarea name="motivo" rows="3" required
                          class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                          placeholder="Describe el motivo de la devolución..."></textarea>
            </div>

            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="document.getElementById('modalDevolucion').classList.add('hidden')"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Cancelar</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    Registrar Devolución
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function cargarProductosVenta() {
    const select  = document.getElementById('ventaSelect');
    const opt     = select.options[select.selectedIndex];
    const prodSel = document.getElementById('productoSelect');
    prodSel.innerHTML = '<option value="">Seleccionar producto...</option>';
    if (!opt.dataset.productos) return;
    const prods = JSON.parse(opt.dataset.productos);
    prods.forEach(p => {
        const o = document.createElement('option');
        o.value = p.id;
        o.textContent = `${p.nombre} (hasta ${p.cantidad} u.)`;
        prodSel.appendChild(o);
    });
}
</script>
@endsection
