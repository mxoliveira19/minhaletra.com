<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class Database
{
    private const REQUIRED_TABLES = ['textos', 'usuarios'];

    private static ?PDO $instance = null;
    private static bool $schemaChecked = false;

    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::assertConfigured();

        $pdo = self::openConnection();

        self::$instance = $pdo;
        self::ensureSchema($pdo);

        return $pdo;
    }

    private static function openConnection(): PDO
    {
        if (DB_AUTO_INIT_SCHEMA === '1') {
            $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, self::pdoOptions());
            $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci', DB_DATABASE));
            $pdo->exec(sprintf('USE `%s`', DB_DATABASE));
            return $pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_DATABASE
        );

        return new PDO($dsn, DB_USERNAME, DB_PASSWORD, self::pdoOptions());
    }

    /**
     * @return array<int, mixed>
     */
    private static function pdoOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ];
    }

    private static function assertConfigured(): void
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

    private static function ensureSchema(PDO $pdo): void
    {
        if (self::$schemaChecked) {
            return;
        }

        $missing = self::missingRequiredTables($pdo);
        if ($missing === []) {
            self::$schemaChecked = true;
            return;
        }

        if (DB_AUTO_INIT_SCHEMA !== '1') {
            throw new RuntimeException(
                'Database schema is missing required tables (' . implode(', ', $missing) . '). ' .
                'Set DB_AUTO_INIT_SCHEMA=1 only for an intentional first initialization, ' .
                'or run scripts/migrate.php from CLI.'
            );
        }

        $schemaFile = dirname(__DIR__, 2) . '/database/schema.sql';
        if (!is_file($schemaFile)) {
            throw new RuntimeException('Schema file not found: ' . $schemaFile);
        }

        $schema = file_get_contents($schemaFile);
        if ($schema === false || trim($schema) === '') {
            throw new RuntimeException('Schema file is empty or unreadable: ' . $schemaFile);
        }

        $pdo->exec($schema);

        $missing = self::missingRequiredTables($pdo);
        if ($missing !== []) {
            throw new RuntimeException('Schema initialization did not create required tables: ' . implode(', ', $missing));
        }

        self::$schemaChecked = true;
    }

    /**
     * @return list<string>
     */
    private static function missingRequiredTables(PDO $pdo): array
    {
        $missing = [];
        foreach (self::REQUIRED_TABLES as $table) {
            $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
            $stmt->execute([$table]);
            if (!$stmt->fetch()) {
                $missing[] = $table;
            }
        }

        return $missing;
    }
}
