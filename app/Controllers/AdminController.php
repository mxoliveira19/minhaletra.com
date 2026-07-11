<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Texto;
use App\Models\Usuario;

final class AdminController
{
    private Texto $textoModel;
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->textoModel = new Texto();
        $this->usuarioModel = new Usuario();
    }

    private function checkAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            header('Location: /admin');
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $senha = $_POST['senha'] ?? '';

            if ($email && !empty($senha)) {
                $user = $this->usuarioModel->autenticar($email, $senha);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_nome'] = $user['nome'];
                    header('Location: /admin');
                    exit;
                } else {
                    $error = 'E-mail ou senha incorretos.';
                }
            } else {
                $error = 'Por favor, preencha todos os campos corretamente.';
            }
        }

        $this->renderAdminView('login', [
            'title' => 'Entrar - Painel Administrativo',
            'error' => $error
        ]);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    public function dashboard(): void
    {
        $this->checkAuth();

        $tipo = $_GET['tipo'] ?? 'poesias';
        $tab = $_GET['tab'] ?? 'ativos'; // 'ativos' ou 'rascunho'

        // Normalize tipo
        if (!in_array($tipo, ['poesias', 'frases', 'cronicas', 'pensamentos', 'teorias'])) {
            $tipo = 'poesias';
        }

        $tipoDb = $tipo;
        if ($tipo === 'cronicas') {
            $tipoDb = 'crônicas';
        }

        $textos = $this->textoModel->allAdmin($tipoDb, $tab);
        $rascunhosCount = $this->textoModel->countAdminByStatus($tipoDb, 'rascunho');

        $this->renderAdminView('dashboard', [
            'title' => 'Painel de Controle - Minha Letra',
            'tipo' => $tipo,
            'tab' => $tab,
            'textos' => $textos,
            'rascunhosCount' => $rascunhosCount,
            'user_nome' => $_SESSION['user_nome'] ?? 'Administrador'
        ]);
    }

    public function novo(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'] ?? 'poesias';
            $modo = $_POST['modo'] ?? 'aleatorio';
            $titulo = trim($_POST['titulo'] ?? '');
            $conteudo = trim($_POST['conteudo'] ?? '');
            $peso = (int)($_POST['peso'] ?? 0);

            // Map frontend tipo to db tipo
            $tipoDb = $tipo;
            if ($tipo === 'cronicas') {
                $tipoDb = 'crônicas';
            } elseif ($tipo === 'poesias') {
                $tipoDb = 'poesia';
            }

            if (!empty($conteudo)) {
                $this->textoModel->save([
                    'tipo' => $tipoDb,
                    'modo' => $modo,
                    'titulo' => $titulo,
                    'conteudo' => $conteudo,
                    'peso' => $peso,
                    'status' => 'publicado'
                ]);
            }
            
            header('Location: /admin?tipo=' . $tipo);
            exit;
        }

        header('Location: /admin');
        exit;
    }

    public function editar(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $tipo = $_POST['tipo'] ?? 'poesias';
            $modo = $_POST['modo'] ?? 'aleatorio';
            $titulo = trim($_POST['titulo'] ?? '');
            $conteudo = trim($_POST['conteudo'] ?? '');
            $peso = (int)($_POST['peso'] ?? 0);
            $status = $_POST['status'] ?? 'publicado';

            $tipoDb = $tipo;
            if ($tipo === 'cronicas') {
                $tipoDb = 'crônicas';
            } elseif ($tipo === 'poesias') {
                $tipoDb = 'poesia';
            }

            $success = false;
            if ($id > 0 && !empty($conteudo)) {
                $success = $this->textoModel->save([
                    'id' => $id,
                    'tipo' => $tipoDb,
                    'modo' => $modo,
                    'titulo' => $titulo,
                    'conteudo' => $conteudo,
                    'peso' => $peso,
                    'status' => $status
                ]);
            }

            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
                exit;
            }

            header('Location: /admin?tipo=' . $tipo . '&tab=' . ($status === 'rascunho' ? 'rascunho' : 'ativos'));
            exit;
        }

        header('Location: /admin');
        exit;
    }

    public function status(): void
    {
        $this->checkAuth();

        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $status = $_GET['status'] ?? $_POST['status'] ?? 'publicado';
        $tipo = $_GET['tipo'] ?? $_POST['tipo'] ?? 'poesias';
        $tab = $_GET['tab'] ?? $_POST['tab'] ?? 'ativos';

        if (!in_array($tab, ['ativos', 'rascunho'], true)) {
            $tab = 'ativos';
        }

        $success = false;
        if ($id > 0) {
            $success = $this->textoModel->updateStatus($id, $status);
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }

        header('Location: /admin?tipo=' . urlencode($tipo) . '&tab=' . urlencode($tab));
        exit;
    }

    public function deletar(): void
    {
        $this->checkAuth();

        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $tipo = $_GET['tipo'] ?? $_POST['tipo'] ?? 'poesias';

        $success = false;
        if ($id > 0) {
            $success = $this->textoModel->delete($id);
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }

        header('Location: /admin?tipo=' . $tipo . '&tab=rascunho');
        exit;
    }

    public function credenciais(): void
    {
        $this->checkAuth();

        $error = null;
        $successMsg = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $nome = trim($_POST['nome'] ?? '');
            $senhaNova = $_POST['senha_nova'] ?? '';
            $senhaConfirmar = $_POST['senha_confirmar'] ?? '';

            if ($email && !empty($nome)) {
                if (!empty($senhaNova)) {
                    if ($senhaNova === $senhaConfirmar) {
                        if (strlen($senhaNova) >= 6) {
                            $res = $this->usuarioModel->atualizar((int)$_SESSION['user_id'], $email, $nome, $senhaNova);
                            if ($res) {
                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_nome'] = $nome;
                                $successMsg = 'Dados e senha atualizados com sucesso!';
                            } else {
                                $error = 'Erro ao atualizar dados.';
                            }
                        } else {
                            $error = 'A nova senha deve ter pelo menos 6 caracteres.';
                        }
                    } else {
                        $error = 'As senhas digitadas não coincidem.';
                    }
                } else {
                    $res = $this->usuarioModel->atualizar((int)$_SESSION['user_id'], $email, $nome);
                    if ($res) {
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_nome'] = $nome;
                        $successMsg = 'Dados atualizados com sucesso!';
                    } else {
                        $error = 'Erro ao atualizar dados.';
                    }
                }
            } else {
                $error = 'Preencha o e-mail e o nome de forma válida.';
            }
        }

        $this->renderAdminView('credenciais', [
            'title' => 'Configurações de Credenciais',
            'error' => $error,
            'successMsg' => $successMsg,
            'email' => $_SESSION['user_email'] ?? '',
            'nome' => $_SESSION['user_nome'] ?? ''
        ]);
    }

    private function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
               (isset($_GET['ajax']) && $_GET['ajax'] == '1') || 
               (isset($_POST['ajax']) && $_POST['ajax'] == '1');
    }

    private function renderAdminView(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../Views/admin/' . $view . '.php';
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            require __DIR__ . '/../Views/layout.php';
        } else {
            http_response_code(500);
            echo "Erro: View Administrativa '{$view}' não encontrada.";
        }
    }
}
