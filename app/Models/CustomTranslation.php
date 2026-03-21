<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTranslation extends Model
{
    protected $fillable = ['locale', 'key', 'value'];

    /**
     * Get custom translations for a locale and group (for PHP groups like 'auth').
     * Returns key => value where key is the short key (e.g. 'failed' for auth.failed).
     */
    public static function getForGroup(string $locale, ?string $group): array
    {
        if ($group === null || $group === '*') {
            return static::getForJson($locale);
        }

        $prefix = $group . '.';
        return static::query()
            ->where('locale', $locale)
            ->where('key', 'like', $prefix . '%')
            ->get()
            ->mapWithKeys(function ($row) use ($prefix) {
                $shortKey = substr($row->key, strlen($prefix));
                return [$shortKey => $row->value];
            })
            ->all();
    }

    /**
     * Get custom translations for JSON (full keys with no dot = JSON keys).
     */
    public static function getForJson(string $locale): array
    {
        return static::query()
            ->where('locale', $locale)
            ->where('key', 'not like', '%.%')
            ->get()
            ->pluck('value', 'key')
            ->all();
    }

    /**
     * Get all custom keys for a locale (for admin form). Returns key => value.
     */
    public static function getAllForLocale(string $locale): array
    {
        return static::query()
            ->where('locale', $locale)
            ->get()
            ->pluck('value', 'key')
            ->all();
    }
}
