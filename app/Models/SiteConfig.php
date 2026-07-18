<?php

declare(strict_types=1);

namespace App\Models;

final class SiteConfig
{
    private string $filePath;

    private array $defaults = [
        'show_frases_joinha_icon' => false,
    ];

    public function __construct()
    {
        $this->filePath = dirname(__DIR__, 2) . '/storage/site_config.json';
    }

    public function showFrasesJoinhaIcon(): bool
    {
        return (bool)$this->all()['show_frases_joinha_icon'];
    }

    public function setShowFrasesJoinhaIcon(bool $show): bool
    {
        $config = $this->all();
        $config['show_frases_joinha_icon'] = $show;

        return $this->write($config);
    }

    private function all(): array
    {
        if (!is_file($this->filePath)) {
            return $this->defaults;
        }

        $contents = file_get_contents($this->filePath);
        if ($contents === false) {
            return $this->defaults;
        }

        $config = json_decode($contents, true);
        if (!is_array($config)) {
            return $this->defaults;
        }

        return array_merge($this->defaults, $config);
    }

    private function write(array $config): bool
    {
        $storageDir = dirname($this->filePath);
        if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true)) {
            return false;
        }

        $payload = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($payload === false) {
            return false;
        }

        return file_put_contents($this->filePath, $payload . PHP_EOL, LOCK_EX) !== false;
    }
}
