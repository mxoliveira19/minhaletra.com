<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only run from CLI.' . PHP_EOL);
}

require __DIR__ . '/../app/Config/config.php';

try {
    assertDatabaseConfig();

    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT),
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ]
    );

    $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci', DB_DATABASE));
    $pdo->exec(sprintf('USE `%s`', DB_DATABASE));
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `migration` varchar(255) NOT NULL PRIMARY KEY,
            `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );

    $migrationDir = __DIR__ . '/../database/migrations';
    $files = glob($migrationDir . '/*.sql') ?: [];
    sort($files, SORT_STRING);

    foreach ($files as $file) {
        $name = basename($file);
        $stmt = $pdo->prepare('SELECT 1 FROM `schema_migrations` WHERE `migration` = ? LIMIT 1');
        $stmt->execute([$name]);
        if ($stmt->fetchColumn()) {
            echo "Skipping already applied migration: {$name}" . PHP_EOL;
            continue;
        }

        $sql = file_get_contents($file);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Migration is empty or unreadable: {$name}");
        }

        echo "Applying migration: {$name}" . PHP_EOL;
        try {
            $pdo->exec($sql);
            $insert = $pdo->prepare('INSERT INTO `schema_migrations` (`migration`) VALUES (?)');
            $insert->execute([$name]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    echo 'Migrations complete.' . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

function assertDatabaseConfig(): void
{
    foreach ([
        'DB_DATABASE' => DB_DATABASE,
        'DB_USERNAME' => DB_USERNAME,
        'DB_PASSWORD' => DB_PASSWORD,
    ] as $name => $value) {
        if ($value === '') {
            throw new RuntimeException($name . ' must be configured in the environment.');
        }
    }
}
