<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Venta::with(['cliente', 'sucursal', 'usuario', 'pagos'])->orderByDesc('fecha');

        if (!empty($this->filters['desde'])) {
            $query->whereDate('fecha', '>=', $this->filters['desde']);
        }
        if (!empty($this->filters['hasta'])) {
            $query->whereDate('fecha', '<=', $this->filters['hasta']);
        }
        if (!empty($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }
        if (!empty($this->filters['sucursal_id'])) {
            $query->where('sucursal_id', $this->filters['sucursal_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Número',
            'Fecha',
            'Cliente',
            'Sucursal',
            'Vendedor',
            'Subtotal',
            'ITBMS',
            'Total',
            'Método Pago',
            'Estado',
        ];
    }

    public function map($venta): array
    {
        return [
            $venta->numero,
            $venta->fecha->format('d/m/Y'),
            $venta->cliente?->nombre_completo ?? 'Consumidor Final',
            $venta->sucursal?->nombre ?? '',
            $venta->usuario?->name ?? '',
            $venta->subtotal,
            $venta->itbms,
            $venta->total,
            $venta->pago_resumen,
            ucfirst($venta->estado),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
