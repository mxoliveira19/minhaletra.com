<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Minha Letra</h2>
            <p>Acesso Restrito ao Painel Administrativo</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/login" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required placeholder="seuemail@exemplo.com" autocomplete="email">
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required placeholder="Digite sua senha" autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-login">Entrar</button>
        </form>
        
        <div class="login-footer">
            <a href="/">&larr; Voltar para o site público</a>
        </div>
    </div>
</div>
