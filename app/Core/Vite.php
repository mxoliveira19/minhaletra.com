<?php

declare(strict_types=1);

namespace App\Core;

final class Vite
{
    public static function asset(string $entry): string
    {
        $manifestPath = __DIR__ . '/../../public/build/.vite/manifest.json';

        if (!file_exists($manifestPath)) {
            return '<script type="module" src="/src/main.js"></script>';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!isset($manifest[$entry])) {
            return '';
        }

        $tags = '';

        if (!empty($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $css) {
                $tags .= '<link rel="stylesheet" href="/build/' . htmlspecialchars($css) . '">' . PHP_EOL;
            }
        }

        $file = $manifest[$entry]['file'];

        $tags .= '<script type="module" src="/build/' . htmlspecialchars($file) . '"></script>' . PHP_EOL;

        return $tags;
    }
}