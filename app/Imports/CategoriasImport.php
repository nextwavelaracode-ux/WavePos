<?php

namespace App\Imports;

use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CategoriasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Soporte de compatibilidad hacia atrás o el nuevo formato
        $principalName = $row['categoria_principal'] ?? $row['categoria_superior'] ?? $row['nombre'] ?? null;
        $subcategoriaName = $row['subcategoria'] ?? null;

        // Si usaron la plantilla vieja (solo "nombre"), y no indicaron superior ni subcategoria, "nombre" es principal
        // Si usaron viejo: nombre="Celulares", superior="Electrónica" -> principal="Electrónica", sub="Celulares"
        if (empty($row['categoria_principal']) && !empty($row['categoria_superior']) && !empty($row['nombre'])) {
            $principalName = $row['categoria_superior'];
            $subcategoriaName = $row['nombre'];
        }

        if (empty($principalName)) {
            return null; // Salta la fila si no hay categoría principal definida
        }

        if (!empty($subcategoriaName)) {
            // Es una subcategoría: Aseguramos que la categoría principal existe
            $parent = Categoria::firstOrCreate(
                ['nombre' => $principalName],
                ['estado' => true, 'impuesto' => '7']
            );

            $nombreAGuardar = $subcategoriaName;
            $parentId = $parent->id;
        } else {
            // Es una categoría principal
            $nombreAGuardar = $principalName;
            $parentId = null;
        }

        $impuesto = $row['itbms_percent'] ?? $row['impuesto'] ?? 7;
        // Limpiar % o espacios si los hay
        if (!is_numeric($impuesto)) {
            $impuesto = preg_replace('/[^0-9]/', '', $impuesto);
        }
        $impuesto = is_numeric($impuesto) ? (string)$impuesto : '7';

        // Check against valid ENUM values
        if (!in_array($impuesto, ['0', '7', '10', '15'])) {
            $impuesto = '7';
        }

        return new Categoria([
            'nombre'              => $nombreAGuardar,
            'descripcion'         => $row['descripcion'] ?? null,
            'parent_id'           => $parentId,
            'impuesto'            => $impuesto,
            'unidad_medida'       => $row['unidad_medida'] ?? null,
            'ubicacion'           => $row['ubicacion'] ?? null,
            'detalle'             => $row['detalle'] ?? null,
            'estado'              => empty($row['estado']) || strtolower($row['estado']) === 'activo' || $row['estado'] == 1,
        ]);
    }

    public function rules(): array
    {
        return [
            // Eliminado para evitar fallos si cambian plantillas. Validamos la lógica en el model().
        ];
    }
}
