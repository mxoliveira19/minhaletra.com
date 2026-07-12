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
                    <li><a href="/teorias" class="nav-link <?= $activeTab === 'teorias' ? 'active' : '' ?>">Teorias</a></li>
                    <li><a href="/sobre" class="nav-link <?= $activeTab === 'sobre' ? 'active' : '' ?>">Sobre</a></li>
                    
                    <?php if ($isAdmin && $activeTab === 'admin'): ?>
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
                <address class="footer-contact" aria-label="Contato do autor">
                    <strong>Maurício de Oliveira</strong>
                    <a href="https://wa.me/5516994640954" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="footer-icon"><path d="M12.04 2a9.9 9.9 0 0 0-8.52 14.96L2.2 22l5.18-1.28A9.95 9.95 0 1 0 12.04 2Zm0 1.8a8.15 8.15 0 1 1 0 16.3 8.03 8.03 0 0 1-4.12-1.13l-.34-.2-3.02.75.78-2.94-.22-.36A8.12 8.12 0 0 1 12.04 3.8Zm-3.5 4.4c-.17 0-.44.06-.67.32-.23.25-.88.86-.88 2.1s.9 2.44 1.03 2.6c.13.17 1.75 2.8 4.32 3.82 2.14.84 2.58.67 3.04.63.47-.04 1.5-.61 1.72-1.2.21-.58.21-1.08.15-1.19-.06-.1-.23-.16-.48-.29-.26-.13-1.5-.74-1.74-.82-.23-.09-.4-.13-.57.13-.17.25-.66.82-.8.98-.15.17-.3.19-.56.06-.25-.13-1.08-.4-2.06-1.27-.76-.68-1.27-1.52-1.42-1.78-.15-.25-.02-.39.11-.52.12-.11.26-.3.39-.45.13-.15.17-.25.26-.42.08-.17.04-.32-.02-.45-.07-.13-.58-1.4-.8-1.91-.2-.5-.41-.43-.57-.44h-.46Z"/></svg>
                        WhatsApp +55 16 9.9464-0954
                    </a>
                    <a href="mailto:mxoliveira73@hotmail.com">
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="footer-icon"><path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm0 2v.35l8 5.1 8-5.1V7H4Zm0 2.72V17h16V9.72l-7.46 4.75a1 1 0 0 1-1.08 0L4 9.72Z"/></svg>
                        E-mail mxoliveira73@hotmail.com
                    </a>
                </address>
                <div class="footer-social" aria-label="Redes sociais">
                    <a href="https://www.facebook.com/profile.php?id=61556823045320" target="_blank" rel="noopener noreferrer" aria-label="Facebook de Maurício de Oliveira">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8.3V6.9c0-.7.5-.9.9-.9H17V2.4c-.37-.05-1.63-.16-3.1-.16-3.07 0-5.17 1.88-5.17 5.33V8.3H5.25v4h3.48V22h4.27v-9.7h3.34l.53-4H13Z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/mauricio_bts/" target="_blank" rel="noopener noreferrer" aria-label="Instagram de Maurício de Oliveira">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm0 2A3.8 3.8 0 0 0 4 7.8v8.4A3.8 3.8 0 0 0 7.8 20h8.4a3.8 3.8 0 0 0 3.8-3.8V7.8A3.8 3.8 0 0 0 16.2 4H7.8Zm4.2 3.2a4.8 4.8 0 1 1 0 9.6 4.8 4.8 0 0 1 0-9.6Zm0 2a2.8 2.8 0 1 0 0 5.6 2.8 2.8 0 0 0 0-5.6Zm5.05-2.65a1.15 1.15 0 1 1 0 2.3 1.15 1.15 0 0 1 0-2.3Z"/></svg>
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Maurício de Oliveira. Todos os direitos reservados.</p>
                <p class="developer-info">Desenvolvido com carinho.</p>
            </div>
        </div>
    </footer>
</body>
</html>
