<?php

namespace App\Http\Controllers;

use App\Models\CustomTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    protected array $locales = ['en', 'si'];

    /**
     * Collect all translation keys from lang files (JSON + PHP groups).
     */
    protected function getAllKeys(): array
    {
        $keys = [];
        $langPath = base_path('lang');
        if (! File::isDirectory($langPath)) {
            return [];
        }

        // JSON files
        foreach ($this->locales as $locale) {
            $jsonPath = $langPath . DIRECTORY_SEPARATOR . $locale . '.json';
            if (File::exists($jsonPath)) {
                $decoded = json_decode(File::get($jsonPath), true);
                if (is_array($decoded)) {
                    $keys = array_merge($keys, array_keys($decoded));
                }
            }
        }

        // PHP groups (e.g. auth, passwords)
        foreach ($this->locales as $locale) {
            $localePath = $langPath . DIRECTORY_SEPARATOR . $locale;
            if (! File::isDirectory($localePath)) {
                continue;
            }
            foreach (File::files($localePath) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                $group = $file->getFilenameWithoutExtension();
                $lines = include $file->getPathname();
                if (is_array($lines)) {
                    $this->flattenKeys($lines, $group, $keys);
                }
            }
        }

        $keys = array_unique($keys);
        sort($keys);

        return array_values($keys);
    }

    protected function flattenKeys(array $lines, string $prefix, array &$keys): void
    {
        foreach ($lines as $k => $v) {
            if (is_array($v)) {
                $this->flattenKeys($v, $prefix . '.' . $k, $keys);
            } else {
                $keys[] = $prefix . '.' . $k;
            }
        }
    }

    /**
     * Get current value for a key in a locale (file first, then custom_translations).
     */
    protected function getValueForLocale(string $locale, string $key): ?string
    {
        $custom = CustomTranslation::where('locale', $locale)->where('key', $key)->first();
        if ($custom) {
            return $custom->value;
        }

        $langPath = base_path('lang');

        // From file: JSON or PHP group
        if (str_contains($key, '.')) {
            [$group, $shortKey] = explode('.', $key, 2);
            $path = $langPath . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $group . '.php';
            if (File::exists($path)) {
                $lines = include $path;
                return data_get($lines, $shortKey);
            }
        } else {
            $jsonPath = $langPath . DIRECTORY_SEPARATOR . $locale . '.json';
            if (File::exists($jsonPath)) {
                $decoded = json_decode(File::get($jsonPath), true);
                return $decoded[$key] ?? null;
            }
        }

        return null;
    }

    /**
     * Group translation keys by page/section (Dashboard, Registered Shops, etc.).
     */
    protected function groupKeysBySection(array $allKeys): array
    {
        $sectionsConfig = config('translation_sections.sections', []);
        $assigned = [];
        $keysBySection = [];

        foreach ($sectionsConfig as $sectionKey => $config) {
            $label = $config['label'] ?? $sectionKey;
            $sectionKeys = $config['keys'] ?? [];
            $prefixes = $config['key_prefixes'] ?? [];
            $catchAll = $config['catch_all'] ?? false;

            $keysBySection[$sectionKey] = [
                'label' => $label,
                'keys' => [],
            ];

            foreach ($allKeys as $key) {
                if (isset($assigned[$key])) {
                    continue;
                }
                $inKeys = in_array($key, $sectionKeys);
                $inPrefix = false;
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($key, $prefix)) {
                        $inPrefix = true;
                        break;
                    }
                }
                if ($inKeys || $inPrefix) {
                    $keysBySection[$sectionKey]['keys'][] = $key;
                    $assigned[$key] = true;
                }
            }
        }

        // Catch-all section (e.g. General) gets any remaining keys
        foreach (array_keys($sectionsConfig) as $sectionKey) {
            $config = $sectionsConfig[$sectionKey];
            if (! empty($config['catch_all'] ?? false)) {
                foreach ($allKeys as $key) {
                    if (! isset($assigned[$key])) {
                        $keysBySection[$sectionKey]['keys'][] = $key;
                        $assigned[$key] = true;
                    }
                }
                break;
            }
        }

        // Remove empty sections
        return array_filter($keysBySection, fn ($s) => count($s['keys']) > 0);
    }

    public function index(Request $request)
    {
        $locales = $this->locales;
        $locale = $request->get('locale', config('app.locale'));
        if (! in_array($locale, $locales)) {
            $locale = $locales[0];
        }

        $allKeys = $this->getAllKeys();
        $translations = [];
        foreach ($allKeys as $key) {
            $translations[$key] = $this->getValueForLocale($locale, $key);
        }

        $keysBySection = $this->groupKeysBySection($allKeys);

        return view('translations.index', [
            'locales' => $locales,
            'currentLocale' => $locale,
            'keysBySection' => $keysBySection,
            'translations' => $translations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,si',
            'translations' => 'nullable|array',
            'translations.*' => 'nullable|string',
        ]);

        $locale = $request->input('locale');
        $translations = $request->input('translations', []);

        foreach ($translations as $key => $value) {
            $key = (string) $key;
            if ($key === '') {
                continue;
            }
            if ($value !== null && $value !== '') {
                CustomTranslation::updateOrCreate(
                    ['locale' => $locale, 'key' => $key],
                    ['value' => $value]
                );
            } else {
                CustomTranslation::where('locale', $locale)->where('key', $key)->delete();
            }
        }

        return redirect()->route('translations.index', ['locale' => $locale])
            ->with('success', __('Translations saved.'));
    }
}
