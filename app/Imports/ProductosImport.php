<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure, SkipsOnError
{
    use Importable;

    private int $importados = 0;
    private array $errores = [];

    /**
     * Maatwebsite normaliza los encabezados a slug (snake_case, sin acentos, sin %).
     * "Nombre"        → "nombre"
     * "SKU"           → "sku"
     * "Código Barras" → "codigo_barras"
     * "Categoría"     → "categoria"
     * "Subcategoría"  → "subcategoria"
     * "Precio Compra" → "precio_compra"
     * "Precio Venta"  → "precio_venta"
     * "Precio Mínimo" → "precio_minimo"
     * "Margen %"      → "margen"
     * "Impuesto %"    → "impuesto"
     * "Stock"         → "stock"
     * "Stock Mínimo"  → "stock_minimo"
     * "Stock Máximo"  → "stock_maximo"
     * "Unidad Medida" → "unidad_medida"
     * "Ubicación"     → "ubicacion"
     * "Estado"        → "estado"
     */
    public function model(array $row)
    {
        // Normalizar: Maatwebsite puede generar keys ligeramente distintos según
        // la versión y el archivo. Mapeamos de forma segura.
        $row = $this->normalizeRow($row);

        // Buscar o crear Categoría
        $categoria = null;
        if (!empty($row['categoria'])) {
            $nombreCat = trim($row['categoria']);
            $categoria = Categoria::whereRaw('LOWER(nombre) = ?', [strtolower($nombreCat)])->first();
            if (!$categoria) {
                $categoria = Categoria::create([
                    'nombre' => $nombreCat,
                    'estado' => true,
                ]);
            }
        } else {
            $categoria = Categoria::firstOrCreate(
                ['nombre' => 'General'],
                ['estado' => true]
            );
        }

        // Buscar o crear Subcategoría si se especificó
        $subcategoria = null;
        if ($categoria && !empty($row['subcategoria'])) {
            $nombreSub = trim($row['subcategoria']);
            $subcategoria = Categoria::whereRaw('LOWER(nombre) = ?', [strtolower($nombreSub)])
                ->where('parent_id', $categoria->id)
                ->first();
            if (!$subcategoria) {
                $subcategoria = Categoria::create([
                    'nombre' => $nombreSub,
                    'parent_id' => $categoria->id,
                    'estado' => true,
                ]);
            }
        }

        $precioCompra = $this->toFloat($row['precio_compra'] ?? null);
        $precioVenta  = $this->toFloat($row['precio_venta'] ?? null);
        $precioMinimo = $this->toFloat($row['precio_minimo'] ?? null, $precioVenta);

        // Si el margen viene en el Excel, usarlo; si no, calcularlo
        $margen = $this->toFloat($row['margen'] ?? null);
        if ($margen == 0 && $precioCompra > 0) {
            $margen = (($precioVenta - $precioCompra) / $precioCompra) * 100;
        }

        // Impuesto enum: '0', '7', '10', '15' (default '7')
        $rawImpuesto = $this->toFloat($row['impuesto'] ?? null, 7);
        $impuestoStr = (string)(int)round($rawImpuesto);
        $impuesto = in_array($impuestoStr, ['0', '7', '10', '15']) ? $impuestoStr : '7';

        $stock       = $this->toInt($row['stock'] ?? null, 0);
        $stockMinimo = $this->toInt($row['stock_minimo'] ?? null, 0);
        $stockMaximo = $this->toInt($row['stock_maximo'] ?? null, null);

        $unidadMedida = !empty($row['unidad_medida']) ? trim($row['unidad_medida']) : 'Unidad (Und)';
        $ubicacion    = !empty($row['ubicacion']) ? trim($row['ubicacion']) : null;

        // Estado: aceptamos "Activo", "1", "true", "si", "sí", o vacío (default activo)
        $estado = true;
        if (isset($row['estado']) && $row['estado'] !== '' && $row['estado'] !== null) {
            $estadoLower = strtolower(trim($row['estado']));
            $estado = in_array($estadoLower, ['activo', '1', 'true', 'si', 'sí', 'yes']);
        }

        $sku = !empty($row['sku']) ? trim($row['sku']) : null;
        $codigoBarras = !empty($row['codigo_barras']) ? trim($row['codigo_barras']) : null;

        $this->importados++;

        return new Producto([
            'nombre'          => trim($row['nombre']),
            'sku'             => $sku,
            'codigo_barras'   => $codigoBarras,
            'categoria_id'    => $categoria->id,
            'subcategoria_id' => $subcategoria?->id,
            'precio_compra'   => $precioCompra,
            'precio_venta'    => $precioVenta,
            'precio_minimo'   => $precioMinimo,
            'margen'          => round($margen, 2),
            'impuesto'        => $impuesto,
            'stock'           => $stock,
            'stock_minimo'    => $stockMinimo,
            'stock_maximo'    => $stockMaximo,
            'unidad_medida'   => $unidadMedida,
            'ubicacion'       => $ubicacion,
            'estado'          => $estado,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'        => 'required|string|max:150',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nombre.required'        => 'El campo "Nombre" es obligatorio.',
            'precio_compra.required'  => 'El campo "Precio Compra" es obligatorio.',
            'precio_compra.numeric'   => 'El campo "Precio Compra" debe ser un número.',
            'precio_venta.required'   => 'El campo "Precio Venta" es obligatorio.',
            'precio_venta.numeric'    => 'El campo "Precio Venta" debe ser un número.',
        ];
    }

    /**
     * Normaliza los keys del row para manejar variantes comunes
     * de los headings tras la conversión de Maatwebsite.
     */
    private function normalizeRow(array $row): array
    {
        $mapped = [];
        foreach ($row as $key => $value) {
            // Normalizar: quitar acentos, %, espacios extra, todo a minúscula
            $normalized = $this->slugKey($key);
            $mapped[$normalized] = $value;
        }

        // Alias para keys comunes que pueden variar
        $aliases = [
            'codigo_barras'  => ['codigo_de_barras', 'cod_barras', 'barcode', 'codigo_barra'],
            'precio_compra'  => ['costo', 'costo_unitario', 'precio_costo'],
            'precio_venta'   => ['precio', 'pvp', 'precio_unitario'],
            'precio_minimo'  => ['precio_min', 'precio_min_venta', 'precio_minimo_venta'],
            'margen'         => ['margen_porcentaje', 'margen_porciento'],
            'impuesto'       => ['impuesto_porcentaje', 'itbms', 'iva', 'tax'],
            'stock_minimo'   => ['stock_min', 'minimo'],
            'stock_maximo'   => ['stock_max', 'maximo'],
            'unidad_medida'  => ['unidad', 'medida', 'und'],
            'ubicacion'      => ['almacen', 'bodega', 'locacion'],
            'categoria'      => ['cat', 'category'],
            'subcategoria'   => ['subcat', 'sub_categoria', 'subcategory'],
        ];

        foreach ($aliases as $canonical => $alternates) {
            if (!isset($mapped[$canonical]) || $mapped[$canonical] === null || $mapped[$canonical] === '') {
                foreach ($alternates as $alt) {
                    if (isset($mapped[$alt]) && $mapped[$alt] !== null && $mapped[$alt] !== '') {
                        $mapped[$canonical] = $mapped[$alt];
                        break;
                    }
                }
            }
        }

        return $mapped;
    }

    /**
     * Convierte un heading a slug: sin acentos, sin %, snake_case.
     */
    private function slugKey(string $key): string
    {
        $key = mb_strtolower(trim($key));
        // Quitar símbolos como %
        $key = str_replace(['%', '#', '(', ')', '.'], '', $key);
        // Reemplazar acentos
        $key = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $key
        );
        // Espacios y guiones a underscore
        $key = preg_replace('/[\s\-]+/', '_', $key);
        // Quitar underscores duplicados y trailing
        $key = preg_replace('/_+/', '_', $key);
        $key = trim($key, '_');

        return $key;
    }

    /**
     * Se ejecuta antes de la validación. Normaliza los encabezados/aliases
     * y sanitiza los campos numéricos para que no fallen por comas o símbolos.
     */
    public function prepareForValidation(array $data, int $index): array
    {
        $data = $this->normalizeRow($data);

        foreach (['precio_compra', 'precio_venta', 'precio_minimo', 'margen', 'impuesto'] as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $val = $this->toFloat($data[$field], -1);
                if ($val >= 0) {
                    $data[$field] = $val;
                }
            }
        }

        foreach (['stock', 'stock_minimo', 'stock_maximo'] as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $data[$field] = $this->toInt($data[$field]);
            }
        }

        return $data;
    }

    private function toFloat($value, float $default = 0): float
    {
        if ($value === null || $value === '') return $default;
        if (is_numeric($value)) return (float)$value;

        // Limpiar espacios, símbolos de moneda
        $str = trim(preg_replace('/[^\d.,-]/', '', (string)$value));
        if ($str === '' || $str === '-') return $default;

        // Si tiene ambos separadores (. y ,)
        if (strpos($str, ',') !== false && strpos($str, '.') !== false) {
            if (strrpos($str, ',') > strrpos($str, '.')) {
                // Formato europeo/latino: 1.250,50
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // Formato anglosajón: 1,250.50
                $str = str_replace(',', '', $str);
            }
        } elseif (strpos($str, ',') !== false) {
            // Solo coma como decimal: 12,50
            $str = str_replace(',', '.', $str);
        }

        return is_numeric($str) ? (float)$str : $default;
    }

    private function toInt($value, ?int $default = 0): ?int
    {
        if ($value === null || $value === '') return $default;
        if (is_numeric($value)) return (int)$value;
        $float = $this->toFloat($value, $default ?? 0);
        return (int)round($float);
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errores[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    public function onError(\Throwable $e)
    {
        $this->errores[] = $e->getMessage();
    }

    public function getImportados(): int
    {
        return $this->importados;
    }

    public function getErrores(): array
    {
        return $this->errores;
    }
}
