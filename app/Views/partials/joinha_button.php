<?php
$joinhasCount = (int)($texto['joinhas_count'] ?? 0);
?>
<button
    type="button"
    class="joinha-button"
    data-texto-id="<?= (int)$texto['id'] ?>"
    aria-label="Dar joinha neste texto"
>
    <span class="joinha-icon" aria-hidden="true">&#128077;</span>
    <span class="joinha-count <?= $joinhasCount > 0 ? '' : 'hidden' ?>"><?= $joinhasCount > 0 ? $joinhasCount : '' ?></span>
</button>
