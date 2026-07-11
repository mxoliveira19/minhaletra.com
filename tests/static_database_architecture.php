<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$checks = [];

$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    'textos_mauricio.sql',
    'Database.php must not reference the old dump.'
);
$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    'INSERT INTO `usuarios`',
    'Database::connect must not create an administrator.'
);
$checks[] = assertContains(
    $root . '/app/Config/config.php',
    "define('DB_AUTO_INIT_SCHEMA', getenv('DB_AUTO_INIT_SCHEMA') ?: '0');",
    'DB_AUTO_INIT_SCHEMA must default to 0.'
);
$checks[] = assertNotMatches(
    $root . '/database/schema.sql',
    '/\b(INSERT|DROP|TRUNCATE|DELETE|REPLACE)\b/i',
    'schema.sql must not contain real data or destructive commands.'
);
$checks[] = assertContains(
    $root . '/scripts/create_admin.php',
    "PHP_SAPI !== 'cli'",
    'create_admin.php must refuse non-CLI execution.'
);
$checks[] = assertContains(
    $root . '/scripts/migrate.php',
    "PHP_SAPI !== 'cli'",
    'migrate.php must refuse non-CLI execution.'
);
$checks[] = assertContains(
    $root . '/scripts/migrate.php',
    'schema_migrations',
    'migrate.php must track applied migrations.'
);

$failed = array_filter($checks, static fn (bool $result): bool => !$result);
if ($failed !== []) {
    fwrite(STDERR, 'Static database architecture checks failed.' . PHP_EOL);
    exit(1);
}

echo 'Static database architecture checks passed.' . PHP_EOL;

function assertContains(string $file, string $needle, string $message): bool
{
    $contents = readFileContents($file);
    if (!str_contains($contents, $needle)) {
        fwrite(STDERR, $message . PHP_EOL);
        return false;
    }
    return true;
}

function assertNotContains(string $file, string $needle, string $message): bool
{
    $contents = readFileContents($file);
    if (str_contains($contents, $needle)) {
        fwrite(STDERR, $message . PHP_EOL);
        return false;
    }
    return true;
}

function assertNotMatches(string $file, string $pattern, string $message): bool
{
    $contents = readFileContents($file);
    if (preg_match($pattern, $contents) === 1) {
        fwrite(STDERR, $message . PHP_EOL);
        return false;
    }
    return true;
}

function readFileContents(string $file): string
{
    $contents = file_get_contents($file);
    if ($contents === false) {
        throw new RuntimeException('Unable to read ' . $file);
    }
    return $contents;
}
