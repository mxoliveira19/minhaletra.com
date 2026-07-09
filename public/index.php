<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Vite.php';

use App\Core\Database;
use App\Core\Vite;

$dbStatus = 'Não testado';

try {
    $pdo = Database::connect();
    $stmt = $pdo->query('SELECT DATABASE() AS db_name');
    $row = $stmt->fetch();

    $dbStatus = 'Conectado ao banco: ' . htmlspecialchars($row['db_name'] ?? 'desconhecido');
} catch (Throwable $e) {
    $dbStatus = 'Erro ao conectar no banco: ' . htmlspecialchars($e->getMessage());
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <title>Minha Letra | Poesias e textos de Maurício de Oliveira</title>
    <meta name="description" content="Poesias, letras e textos autorais de Maurício de Oliveira.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="canonical" href="https://minhaletra.com/">

    <?= Vite::asset('src/main.js') ?>
</head>
<body>
    <main class="page">
        <section class="hero">
            <h1>Minha Letra</h1>
            <p>Poesias, letras e textos autorais de Maurício de Oliveira.</p>
        </section>

        <section class="card">
            <h2>Site PHP com Docker, MariaDB e Vite</h2>
            <p>Se você está vendo esta página, o PHP está funcionando.</p>

            <div class="status">
                <?= $dbStatus ?>
            </div>
        </section>
    </main>
</body>
</html>