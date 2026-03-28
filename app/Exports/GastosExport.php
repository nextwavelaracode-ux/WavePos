<?php

namespace App\Exports;

use App\Models\Gasto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GastosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Gasto::with(['categoria', 'sucursal', 'usuario'])->orderByDesc('fecha');

        if (!empty($this->filters['fecha_desde'])) {
            $query->where('fecha', '>=', $this->filters['fecha_desde']);
        }
        if (!empty($this->filters['fecha_hasta'])) {
            $query->where('fecha', '<=', $this->filters['fecha_hasta']);
        }
        if (!empty($this->filters['categoria_id'])) {
            $query->where('categoria_gasto_id', $this->filters['categoria_id']);
        }
        if (!empty($this->filters['sucursal_id'])) {
            $query->where('sucursal_id', $this->filters['sucursal_id']);
        }
        if (!empty($this->filters['metodo'])) {
            $query->where('metodo_pago', $this->filters['metodo']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Categoría',
            'Monto',
            'Método Pago',
            'Referencia',
            'Fecha',
            'Sucursal',
            'Usuario',
            'Descripción',
            'Estado',
        ];
    }

    public function map($gasto): array
    {
        return [
            $gasto->id,
            $gasto->categoria?->nombre ?? '',
            $gasto->monto,
            $gasto->metodo_pago_label,
            $gasto->referencia,
            $gasto->fecha->format('d/m/Y'),
            $gasto->sucursal?->nombre ?? '',
            $gasto->usuario?->name ?? '',
            $gasto->descripcion,
            ucfirst($gasto->estado),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
