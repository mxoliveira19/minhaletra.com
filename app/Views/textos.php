<?php
$titleDisplay = ucfirst($tipo);
if ($tipo === 'poesias') {
    $titleDisplay = 'Poesias';
} elseif ($tipo === 'cronicas') {
    $titleDisplay = 'Crônicas';
} elseif ($tipo === 'pensamentos') {
    $titleDisplay = 'Pensamentos';
} elseif ($tipo === 'frases') {
    $titleDisplay = 'Frases';
}

// Generate JSON-LD Schema.org for SEO
$schemas = [];
foreach ($textos as $t) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => ($tipo === 'frases') ? "Quotation" : "BlogPosting",
        "author" => [
            "@type" => "Person",
            "name" => "Maurício de Oliveira"
        ],
        "headline" => !empty($t['titulo']) ? $t['titulo'] : ($tipo === 'frases' ? "Aforismo de Maurício de Oliveira" : "Texto de Maurício de Oliveira"),
        "text" => htmlspecialchars($t['conteudo']),
        "datePublished" => $t['data_publicacao']
    ];
    $schemas[] = $schema;
}
?>

<!-- Inject JSON-LD to head -->
<script type="application/ld+json">
<?= json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<div class="category-header">
    <div class="container">
        <h1><?= htmlspecialchars($titleDisplay) ?></h1>
        <p class="category-subtitle">
            <?php if ($tipo === 'poesias'): ?>
                Versos, ritmos e expressões poéticas.
            <?php elseif ($tipo === 'frases'): ?>
                Aforismos breves e pensamentos condensados.
            <?php elseif ($tipo === 'crônicas'): ?>
                Crônicas sobre a vida cotidiana e reflexões saudosas.
            <?php else: ?>
                Reflexões sobre a razão, a ciência e a sociedade.
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="textos-container container">
    <?php if (empty($textos)): ?>
        <div class="empty-state">
            <p>Nenhum texto publicado nesta categoria no momento.</p>
        </div>
    <?php else: ?>
        <div class="textos-list <?= $tipo === 'frases' ? 'frases-grid' : 'textos-flow' ?>">
            <?php foreach ($textos as $index => $texto): ?>
                <?php 
                $hasTitle = !empty($texto['titulo']) && $texto['titulo'] !== '***'; 
                $titleText = $hasTitle ? $texto['titulo'] : '';
                ?>
                
                <?php if ($tipo === 'frases'): ?>
                    <!-- Phrase Layout -->
                    <article class="phrase-card" id="texto-<?= $texto['id'] ?>">
                        <div class="phrase-body">
                            <span class="quote-mark">&ldquo;</span>
                            <p><?= nl2br(htmlspecialchars($texto['conteudo'])) ?></p>
                        </div>
                        <?php if ($hasTitle): ?>
                            <h3 class="phrase-title">&mdash; <?= htmlspecialchars($titleText) ?></h3>
                        <?php else: ?>
                            <h3 class="phrase-author">&mdash; Maurício de Oliveira</h3>
                        <?php endif; ?>
                    </article>

                <?php elseif ($tipo === 'poesias' || $tipo === 'poesia'): ?>
                    <!-- Poetry Layout -->
                    <article class="poetry-card" id="texto-<?= $texto['id'] ?>">
                        <?php if ($hasTitle): ?>
                            <h2 class="poetry-title"><?= htmlspecialchars($titleText) ?></h2>
                        <?php else: ?>
                            <div class="poetry-divider">***</div>
                        <?php endif; ?>
                        
                        <div class="poetry-content"><?= htmlspecialchars(trim($texto['conteudo'])) ?></div>
                    </article>

                <?php else: ?>
                    <!-- Chronicle or Thought Layout -->
                    <article class="article-card" id="texto-<?= $texto['id'] ?>">
                        <?php if ($hasTitle): ?>
                            <h2 class="article-title"><?= htmlspecialchars($titleText) ?></h2>
                        <?php else: ?>
                            <div class="article-divider">***</div>
                        <?php endif; ?>
                        
                        <div class="article-meta">
                            <span class="author-name">Maurício de Oliveira</span>
                            <?php if (!empty($texto['data_publicacao'])): ?>
                                <span class="bullet">&bull;</span>
                                <time datetime="<?= $texto['data_publicacao'] ?>"><?= date('d/m/Y', strtotime($texto['data_publicacao'])) ?></time>
                            <?php endif; ?>
                        </div>

                        <div class="article-content">
                            <?php 
                            // Split by double line breaks to form paragraphs
                            $paragraphs = explode("\n\n", $texto['conteudo']);
                            foreach ($paragraphs as $para) {
                                if (trim($para) !== '') {
                                    echo '<p>' . nl2br(htmlspecialchars(trim($para))) . '</p>';
                                }
                            }
                            ?>
                        </div>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
