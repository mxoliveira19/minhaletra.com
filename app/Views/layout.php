<?php
use App\Core\Vite;
$activeTab = $activeTab ?? '';
$isAdmin = isset($_SESSION['user_id']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Optimization -->
    <title><?= htmlspecialchars($title ?? 'Minha Letra | Maurício de Oliveira') ?></title>
    <meta name="description" content="<?= htmlspecialchars($description ?? 'Site do escritor Maurício de Oliveira com poesias, frases, crônicas e pensamentos.') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical ?? APP_URL) ?>">
    
    <!-- OpenGraph Tags for Social Sharing -->
    <meta property="og:title" content="<?= htmlspecialchars($title ?? 'Minha Letra | Maurício de Oliveira') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description ?? 'Site do escritor Maurício de Oliveira com poesias, frases, crônicas e pensamentos.') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonical ?? APP_URL) ?>">
    <meta property="og:site_name" content="Minha Letra">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">

    <!-- Vite Compiled Assets -->
    <?= Vite::asset('src/main.js') ?>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo">
                <span class="logo-text">Minha Letra</span>
                <span class="logo-subtext">Maurício de Oliveira</span>
            </a>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <nav class="navbar" id="navbar">
                <ul class="nav-menu">
                    <li><a href="/" class="nav-link <?= $activeTab === 'home' ? 'active' : '' ?>">Home</a></li>
                    <li><a href="/poesias" class="nav-link <?= $activeTab === 'poesias' ? 'active' : '' ?>">Poesias</a></li>
                    <li><a href="/frases" class="nav-link <?= $activeTab === 'frases' ? 'active' : '' ?>">Frases</a></li>
                    <li><a href="/cronicas" class="nav-link <?= $activeTab === 'cronicas' ? 'active' : '' ?>">Crônicas</a></li>
                    <li><a href="/pensamentos" class="nav-link <?= $activeTab === 'pensamentos' ? 'active' : '' ?>">Pensamentos</a></li>
                    <li><a href="/sobre" class="nav-link <?= $activeTab === 'sobre' ? 'active' : '' ?>">Sobre</a></li>
                    
                    <?php if ($isAdmin): ?>
                        <li class="admin-item"><a href="/admin" class="nav-link admin-badge <?= $activeTab === 'admin' ? 'active' : '' ?>">Painel Admin</a></li>
                        <li><a href="/admin/logout" class="nav-link logout-link">Sair</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-info">
                <h3>Minha Letra</h3>
                <p>Poesias, frases, crônicas e reflexões filosóficas de Maurício de Oliveira.</p>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Maurício de Oliveira. Todos os direitos reservados.</p>
                <p class="developer-info">Desenvolvido com carinho.</p>
            </div>
        </div>
    </footer>
</body>
</html>
