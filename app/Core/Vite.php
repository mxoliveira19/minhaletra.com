<?php

declare(strict_types=1);

namespace App\Core;

final class Vite
{
    public static function asset(string $entry): string
    {
        $manifestPath = self::manifestPath();

        if (!file_exists($manifestPath)) {
            return '';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!is_array($manifest) || !isset($manifest[$entry])) {
            return '';
        }

        $tags = '';

        if (!empty($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $css) {
                $tags .= '<link rel="stylesheet" href="' . self::buildUrl($css) . '">' . PHP_EOL;
            }
        }

        $file = $manifest[$entry]['file'];

        $tags .= '<script type="module" src="' . self::buildUrl($file) . '"></script>' . PHP_EOL;

        return $tags;
    }

    private static function manifestPath(): string
    {
        $buildPath = __DIR__ . '/../../public/build';
        $manifestPath = $buildPath . '/manifest.json';

        if (file_exists($manifestPath)) {
            return $manifestPath;
        }

        return $buildPath . '/.vite/manifest.json';
    }

    private static function buildUrl(string $asset): string
    {
        return '/build/' . htmlspecialchars($asset, ENT_QUOTES, 'UTF-8');
    }
}
