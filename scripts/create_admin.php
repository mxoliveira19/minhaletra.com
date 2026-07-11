<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only run from CLI.' . PHP_EOL);
}

require __DIR__ . '/../app/Config/config.php';

$options = getopt('', ['email:', 'name:', 'password::', 'update']);
$email = trim((string)($options['email'] ?? getenv('ADMIN_EMAIL') ?: ''));
$name = trim((string)($options['name'] ?? getenv('ADMIN_NAME') ?: ''));
$password = (string)($options['password'] ?? getenv('ADMIN_PASSWORD') ?: '');
$allowUpdate = array_key_exists('update', $options);

try {
    assertAdminInput($email, $name, $password);
    assertDatabaseConfig();

    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_DATABASE),
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ]
    );

    $stmt = $pdo->prepare('SELECT `id` FROM `usuarios` WHERE `email` = ? LIMIT 1');
    $stmt->execute([$email]);
    $existingId = $stmt->fetchColumn();

    if ($existingId && !$allowUpdate) {
        fwrite(STDERR, 'Administrator already exists. Use --update to replace name/password intentionally.' . PHP_EOL);
        exit(1);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($existingId) {
        $stmt = $pdo->prepare('UPDATE `usuarios` SET `nome` = ?, `senha` = ? WHERE `id` = ?');
        $stmt->execute([$name, $hash, $existingId]);
        echo 'Administrator updated.' . PHP_EOL;
    } else {
        $stmt = $pdo->prepare('INSERT INTO `usuarios` (`email`, `senha`, `nome`) VALUES (?, ?, ?)');
        $stmt->execute([$email, $hash, $name]);
        echo 'Administrator created.' . PHP_EOL;
    }

    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, 'Create admin failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

function assertAdminInput(string $email, string $name, string $password): void
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('A valid --email or ADMIN_EMAIL is required.');
    }
    if ($name === '') {
        throw new RuntimeException('--name or ADMIN_NAME is required.');
    }
    if ($password === '') {
        throw new RuntimeException('--password or ADMIN_PASSWORD is required.');
    }
    if (strlen($password) < 12) {
        throw new RuntimeException('Password must be at least 12 characters.');
    }
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
