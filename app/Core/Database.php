<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

final class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT
        );

        // Connect without database name to ensure the database can be created if missing
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);

        $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci', DB_DATABASE));
        $pdo->exec(sprintf('USE `%s`', DB_DATABASE));

        self::$instance = $pdo;

        try {
            self::setup($pdo);
        } catch (Throwable $e) {
            // Fail silently or handle if database is in locked state
            error_log('Database setup error: ' . $e->getMessage());
        }

        return $pdo;
    }

    private static function setup(PDO $pdo): void
    {
        // 1. Check if table `textos` exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'textos'");
        $textosExists = (bool)$stmt->fetch();

        if (!$textosExists) {
            $sqlFile = __DIR__ . '/../../database/textos_mauricio.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                if (!empty($sql)) {
                    $pdo->exec($sql);
                }
            }
        }

        // 2. Ensure column `status` exists in `textos`
        $stmt = $pdo->query("SHOW COLUMNS FROM `textos` LIKE 'status'");
        $statusExists = (bool)$stmt->fetch();
        if (!$statusExists) {
            $pdo->exec("ALTER TABLE `textos` ADD COLUMN `status` VARCHAR(20) DEFAULT 'publicado'");
        }

        // 3. Ensure table `usuarios` exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
        $usuariosExists = (bool)$stmt->fetch();

        if (!$usuariosExists) {
            $pdo->exec("CREATE TABLE `usuarios` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `senha` VARCHAR(255) NOT NULL,
                `nome` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed initial administrator
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmtInsert = $pdo->prepare("INSERT INTO `usuarios` (`email`, `senha`, `nome`) VALUES (?, ?, ?)");
            $stmtInsert->execute(['mxoliveira73@hotmail.com', $hashedPassword, 'Maurício de Oliveira']);
        }
    }
}