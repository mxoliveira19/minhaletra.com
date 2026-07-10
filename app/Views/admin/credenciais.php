<div class="admin-container container">
    <header class="admin-header">
        <div>
            <h1>Configurações da Conta</h1>
            <p>Gerencie suas credenciais de acesso administrativas.</p>
        </div>
        <div class="admin-header-actions">
            <a href="/admin" class="btn btn-secondary">&larr; Voltar ao Painel</a>
        </div>
    </header>

    <div class="credenciais-card card">
        <h3>Alterar E-mail e Senha</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/credenciais" method="POST" class="admin-form">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($nome) ?>">
            </div>

            <div class="form-group">
                <label for="email">E-mail do Administrador (Nome de Usuário)</label>
                <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email) ?>">
            </div>

            <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 30px 0;">

            <div class="alert alert-info">
                Deixe os campos de senha em branco caso queira manter a senha atual.
            </div>

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="senha_nova">Nova Senha</label>
                    <input type="password" name="senha_nova" id="senha_nova" placeholder="Nova senha (mínimo 6 caracteres)">
                </div>
                
                <div class="form-group flex-1">
                    <label for="senha_confirmar">Confirmar Nova Senha</label>
                    <input type="password" name="senha_confirmar" id="senha_confirmar" placeholder="Repita a nova senha">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <a href="/admin" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
