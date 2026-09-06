<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    // ─── Cache key ────────────────────────────────────────
    private const CACHE_KEY = 'app_settings';

    /**
     * Invalidate the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Return all settings as a flat key→value array (cached).
     */
    public static function all_cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a single setting value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all_cached()[$key] ?? $default;
    }

    /**
     * Save (insert or update) a single setting.
     */
    public static function set(string $key, mixed $value): void
    {
        $group = static::inferGroupFromKey($key);
        static::updateOrCreate(
            ['key' => $key], 
            ['value' => $value, 'group' => $group]
        );
        static::clearCache();
    }

    /**
     * Infer the setting group based on its key prefix.
     */
    private static function inferGroupFromKey(string $key): string
    {
        $map = [
            'pos_' => 'pos',
            'caja_' => 'caja',
            'ventas_' => 'ventas',
            'compras_' => 'compras',
            'inv_' => 'inventario',
            'clientes_' => 'clientes',
            'pago_' => 'pagos',
            'seg_' => 'seguridad',
            'rep_' => 'reportes',
            'itbms_' => 'impuestos',
        ];

        foreach ($map as $prefix => $group) {
            if (str_starts_with($key, $prefix)) {
                return $group;
            }
        }

        return 'general';
    }

    /**
     * Return all settings for a given group as key→value Collection.
     */
    public static function group(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->pluck('value', 'key');
    }

    /**
     * Return every group's settings indexed by group → [key → value].
     */
    public static function allGrouped(): array
    {
        $rows = static::all();
        $result = [];
        foreach ($rows as $row) {
            $result[$row->group][$row->key] = $row->value;
        }
        return $result;
    }
}
