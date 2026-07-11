<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$checks = [];

$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    'textos_mauricio.sql',
    'Database.php must not reference the old dump.'
);
$checks[] = assertContains(
    $root . '/app/Core/Database.php',
    "private const REQUIRED_TABLES = ['textos', 'usuarios'];",
    'Database.php must require textos and usuarios for normal connections.'
);
$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    "private const REQUIRED_TABLES = ['textos', 'usuarios', 'poems'];",
    'poems must not be required for normal connections.'
);
$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    "SHOW TABLES LIKE 'poems'",
    'Database.php must not block normal connections when poems is absent.'
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
    $root . '/database/schema.sql',
    'CREATE TABLE IF NOT EXISTS `poems`',
    'schema.sql must keep the versioned poems table for new installations.'
);
$checks[] = assertContains(
    $root . '/database/migrations/003_create_poems.sql',
    'CREATE TABLE IF NOT EXISTS `poems`',
    '003_create_poems.sql must remain available for explicit future application.'
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
$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    'schema_migrations',
    'Database::connect must not run migrations automatically.'
);
$checks[] = assertNotContains(
    $root . '/app/Core/Database.php',
    '/database/migrations',
    'Database::connect must not load migration files automatically.'
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
