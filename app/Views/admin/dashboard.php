<?php
$activeTab = 'admin'; // For layout navbar
?>

<div class="admin-container container">
    <header class="admin-header">
        <div>
            <h1>Painel de Controle</h1>
            <p>Bem-vindo, <strong><?= htmlspecialchars($user_nome) ?></strong>. Gerencie os textos do site.</p>
        </div>
        <div class="admin-header-actions">
            <a href="/admin/credenciais" class="btn btn-secondary">Credenciais</a>
            <a href="/admin/logout" class="btn btn-danger">Sair</a>
        </div>
    </header>

    <!-- Content Type Tabs -->
    <nav class="admin-tabs">
        <a href="/admin?tipo=poesias" class="tab-link <?= $tipo === 'poesias' ? 'active' : '' ?>">Poesias</a>
        <a href="/admin?tipo=frases" class="tab-link <?= $tipo === 'frases' ? 'active' : '' ?>">Frases</a>
        <a href="/admin?tipo=cronicas" class="tab-link <?= $tipo === 'cronicas' ? 'active' : '' ?>">Crônicas</a>
        <a href="/admin?tipo=pensamentos" class="tab-link <?= $tipo === 'pensamentos' ? 'active' : '' ?>">Pensamentos</a>
        <a href="/admin?tipo=teorias" class="tab-link <?= $tipo === 'teorias' ? 'active' : '' ?>">Teorias</a>
    </nav>

    <!-- Sub-navigation: Ativos / Rascunhos & Novo -->
    <div class="admin-sub-bar">
        <div class="status-filters">
            <a href="/admin?tipo=<?= $tipo ?>&tab=ativos" class="filter-btn <?= $tab === 'ativos' ? 'active' : '' ?>">
                Publicados
            </a>
            <a href="/admin?tipo=<?= $tipo ?>&tab=rascunho" class="filter-btn <?= $tab === 'rascunho' ? 'active' : '' ?>">
                Rascunhos (<?= (int)$rascunhosCount ?>)
            </a>
        </div>
        <div>
            <button class="btn btn-success" id="btnToggleNewForm">+ Novo Texto</button>
        </div>
    </div>

    <!-- New Text Form (Initially Hidden) -->
    <div class="admin-new-card card hidden" id="newTextFormCard">
        <h3>Cadastrar Novo Texto (<?= ucfirst($tipo === 'cronicas' ? 'crônica' : ($tipo === 'poesias' ? 'poesia' : rtrim($tipo, 's'))) ?>)</h3>
        <form action="/admin/novo" method="POST" class="admin-form">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
            
            <div class="form-row">
                <div class="form-group flex-3">
                    <label for="new_titulo">Título (Opcional para Frases)</label>
                    <input type="text" name="titulo" id="new_titulo" placeholder="Digite o título...">
                </div>
                <div class="form-group flex-1">
                    <label for="new_modo">Modo</label>
                    <select name="modo" id="new_modo">
                        <option value="aleatorio">Aleatório</option>
                        <option value="fixo">Fixo</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="new_peso">Peso</label>
                    <input type="number" name="peso" id="new_peso" value="0" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="new_conteudo">Conteúdo</label>
                <textarea name="conteudo" id="new_conteudo" rows="8" required placeholder="Escreva o texto aqui..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="btnCancelNewForm">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Texto</button>
            </div>
        </form>
    </div>

    <!-- Listings -->
    <div class="admin-listing card">
        <?php if (empty($textos)): ?>
            <div class="admin-empty-state">
                <p>Nenhum texto nesta aba no momento.</p>
            </div>
        <?php else: ?>
            <div class="admin-table-header">
                <div class="col-title">Título</div>
                <div class="col-preview">Prévia do Texto</div>
                <div class="col-mode">Modo</div>
                <div class="col-weight">Peso</div>
                <div class="col-actions">Ações</div>
            </div>

            <div class="admin-rows-list">
                <?php foreach ($textos as $texto): ?>
                    <?php
                    // Compute the preview based on rules
                    $lines = explode("\n", $texto['conteudo']);
                    $previewText = '';
                    if ($tipo === 'poesias') {
                        $previewText = implode("\n", array_slice($lines, 0, 5));
                    } elseif ($tipo === 'frases') {
                        $previewText = $texto['conteudo'];
                    } else {
                        $previewText = mb_strlen($texto['conteudo']) > 300 ? mb_substr($texto['conteudo'], 0, 300) . '...' : $texto['conteudo'];
                    }
                    ?>
                    
                    <div class="admin-row-item" id="row-<?= $texto['id'] ?>">
                        <!-- Full editable form for the text -->
                        <form action="/admin/editar" method="POST" class="admin-row-form" data-id="<?= $texto['id'] ?>">
                            <input type="hidden" name="id" value="<?= $texto['id'] ?>">
                            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($texto['status']) ?>">

                            <div class="row-fields-visible">
                                <!-- Title -->
                                <div class="col-title">
                                    <textarea name="titulo" class="inline-input-title" rows="1" placeholder="Sem título"><?= htmlspecialchars($texto['titulo']) ?></textarea>
                                </div>

                                <!-- Text Preview (Read-only on visible layout) -->
                                <div class="col-preview">
                                    <div class="preview-text-box" title="Clique em Editar para alterar o texto"><?= nl2br(htmlspecialchars($previewText)) ?></div>
                                </div>

                                <!-- Mode -->
                                <div class="col-mode">
                                    <select name="modo" class="inline-select-modo">
                                        <option value="aleatorio" <?= $texto['modo'] === 'aleatorio' ? 'selected' : '' ?>>Aleatório</option>
                                        <option value="fixo" <?= $texto['modo'] === 'fixo' ? 'selected' : '' ?>>Fixo</option>
                                    </select>
                                </div>

                                <!-- Weight -->
                                <div class="col-weight">
                                    <input type="number" name="peso" value="<?= $texto['peso'] ?>" class="inline-input-peso" min="0">
                                </div>

                                <!-- Actions -->
                                <div class="col-actions">
                                    <?php if ($tab === 'ativos'): ?>
                                        <!-- Send to draft -->
                                        <a href="/admin/status?id=<?= $texto['id'] ?>&status=rascunho&tipo=<?= $tipo ?>" class="action-btn btn-draft" title="Mandar para rascunhos">Rascunho</a>
                                        <!-- Toggle full editor -->
                                        <button type="button" class="action-btn btn-edit btn-toggle-editor" data-id="<?= $texto['id'] ?>">Editar</button>
                                    <?php else: ?>
                                        <!-- Restore to site -->
                                        <a href="/admin/status?id=<?= $texto['id'] ?>&status=publicado&tipo=<?= $tipo ?>&tab=rascunho" class="action-btn btn-restore" title="Restituir no site">Restituir</a>
                                        <!-- Permanent delete -->
                                        <a href="/admin/deletar?id=<?= $texto['id'] ?>&tipo=<?= $tipo ?>" class="action-btn btn-delete btn-confirm-delete" title="Deletar permanentemente">Deletar</a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Expanded Editor (Initially Collapsed) -->
                            <?php if ($tab === 'ativos'): ?>
                                <div class="row-editor-expanded collapsed" id="editor-<?= $texto['id'] ?>">
                                    <div class="editor-header">
                                        <h4>Editar Texto Completo</h4>
                                    </div>
                                    <div class="form-group">
                                        <textarea name="conteudo" rows="12" class="expanded-textarea" required><?= htmlspecialchars($texto['conteudo']) ?></textarea>
                                    </div>
                                    <div class="editor-actions">
                                        <!-- Quick inline save status display -->
                                        <span class="save-status" id="status-msg-<?= $texto['id'] ?>"></span>
                                        <button type="button" class="btn btn-secondary btn-close-editor" data-id="<?= $texto['id'] ?>">Cancelar</button>
                                        <button type="submit" class="btn btn-primary btn-save-text" data-id="<?= $texto['id'] ?>">Salvar</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
