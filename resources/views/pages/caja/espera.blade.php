@extends('layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-screen-xl md:p-6">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Ventas en Espera</h2>
            <p class="text-sm text-gray-500">Ventas pausadas pendientes de completar</p>
        </div>
        <a href="{{ route('caja.pos') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition">
            Ir al POS
        </a>
    </div>

    @if($ventasEspera->isEmpty())
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 flex flex-col items-center justify-center py-20 text-center">
            <svg class="h-16 w-16 text-gray-200 dark:text-gray-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-base font-semibold text-gray-500 dark:text-gray-400">No hay ventas en espera</p>
            <p class="mt-1 text-sm text-gray-400">Usa el botón "Pausar" en el POS para guardar una venta.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($ventasEspera as $espera)
            <div class="rounded-2xl border border-amber-200 bg-white dark:border-amber-800 dark:bg-gray-900 shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                        <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 dark:text-white truncate">{{ $espera->nombre }}</p>
                        <p class="text-xs text-gray-400">{{ $espera->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-3 space-y-1">
                        @foreach(array_slice($espera->carrito, 0, 3) as $item)
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                            <span class="truncate flex-1">{{ $item['nombre'] }}</span>
                            <span class="ml-2 font-medium">x{{ $item['cantidad'] }}</span>
                        </div>
                        @endforeach
                        @if(count($espera->carrito) > 3)
                        <p class="text-xs text-gray-400">+ {{ count($espera->carrito) - 3 }} más...</p>
                        @endif
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-800 dark:text-white border-t border-gray-100 dark:border-gray-800 pt-2 mb-3">
                        <span>Total estimado</span>
                        <span>${{ number_format($espera->total_carrito, 2) }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="retomar({{ $espera->id }})"
                                class="flex-1 rounded-lg bg-brand-500 py-2 text-xs font-semibold text-white hover:bg-brand-600 transition">
                            ▶ Retomar
                        </button>
                        <button onclick="eliminarEspera({{ $espera->id }})"
                                class="rounded-lg border border-red-300 px-3 py-2 text-xs font-semibold text-red-500 hover:bg-red-50 transition">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<script>
async function retomar(id) {
    const r = await fetch(`/caja/espera/${id}/retomar`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    const data = await r.json();
    if (data.success) {
        sessionStorage.setItem('carrito_retomado', JSON.stringify(data.carrito));
        Swal.fire({ icon: 'success', title: '¡Venta retomada!', text: 'Redirigiendo al POS...', timer: 1500, showConfirmButton: false })
            .then(() => window.location.href = '{{ route('caja.pos') }}');
    }
}
async function eliminarEspera(id) {
    const c = await Swal.fire({ icon: 'question', title: '¿Eliminar venta en espera?', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar' });
    if (!c.isConfirmed) return;
    const r = await fetch(`/caja/espera/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    const data = await r.json();
    if (data.success) location.reload();
}
</script>
@endsection
