<?php

namespace App\Translation;

use App\Models\CustomTranslation;
use Illuminate\Translation\FileLoader;

class MergeTranslationLoader extends FileLoader
{
    /**
     * Load the messages for the given locale, merging DB overrides with file-based translations.
     */
    public function load($locale, $group, $namespace = null): array
    {
        $lines = parent::load($locale, $group, $namespace);

        $overrides = $group === '*'
            ? CustomTranslation::getForJson($locale)
            : CustomTranslation::getForGroup($locale, $group);

        return array_merge($lines, $overrides);
    }

    /**
     * Load a locale from the JSON file path, merging DB overrides.
     */
    public function loadJsonPath(string $path): array
    {
        $lines = parent::loadJsonPath($path);

        if (preg_match('#[/\\\\]([a-z]{2})\.json$#', $path, $m)) {
            $locale = $m[1];
            $overrides = CustomTranslation::getForJson($locale);
            $lines = array_merge($lines, $overrides);
        }

        return $lines;
    }
}
