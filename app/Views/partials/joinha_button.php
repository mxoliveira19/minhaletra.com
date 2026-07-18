<?php
$joinhasCount = (int)($texto['joinhas_count'] ?? 0);
$showJoinhaIcon = ($tipo ?? '') !== 'frases' || !empty($showFrasesJoinhaIcon);
?>
<button
    type="button"
    class="joinha-button"
    data-texto-id="<?= (int)$texto['id'] ?>"
    aria-label="Dar joinha neste texto"
>
    <?php if ($showJoinhaIcon): ?>
        <span class="joinha-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" focusable="false">
                <path d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z" />
            </svg>
        </span>
    <?php endif; ?>
    <span class="joinha-count <?= $joinhasCount > 0 ? '' : 'hidden' ?>"><?= $joinhasCount > 0 ? $joinhasCount : '' ?></span>
</button>
