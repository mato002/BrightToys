<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? static::castValue($setting->value, $setting->type) : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, $type)
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        $settings = static::where('group', $group)->get();
        if ($settings->isEmpty()) {
            return [];
        }
        return $settings->mapWithKeys(function ($setting) {
            return [$setting->key => static::castValue($setting->value, $setting->type)];
        })->toArray();
    }
}
