<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            try {
                if (!Schema::hasTable('settings')) {
                    return $default;
                }
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        });
    }

    public static function set(string $key, $value, string $group = 'general')
    {
        Cache::forget("setting_{$key}");
        Cache::forget("all_settings");
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    public static function getAllSettings(): array
    {
        $defaults = [
            // Admin Panel branding and theme
            'store_name' => 'Admin Console',
            'theme_mode' => 'system', // light, dark, system
            'theme_primary_color' => '#0f172a',
            'theme_hover_color' => '#334155',
            'btn_primary_bg' => '#0f172a',
            'btn_primary_text' => '#ffffff',
            'btn_primary_hover' => '#1e293b',
            'btn_accent_bg' => '#10b981',
            'btn_accent_text' => '#ffffff',
            'sidebar_bg_color' => '#000000',
            'sidebar_active_color' => '#add8e6',
            'sidebar_text_color' => '#ffffff',
            'footer_copyright_prefix' => '© ' . date('Y') . ', made with ❤️ by',
            'footer_creator_name' => 'Decent Infoways',
            'footer_creator_url' => 'https://decentinfoways.com',
        ];

        try {
            if (Schema::hasTable('settings')) {
                $settings = self::all()->pluck('value', 'key')->toArray();
                return array_merge($defaults, $settings);
            }
        } catch (\Throwable $e) {
            // fallback to defaults if database is not migrated yet
        }

        return $defaults;
    }
}
