<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Texto;
use App\Models\SiteConfig;

final class SiteController
{
    private Texto $textoModel;
    private SiteConfig $siteConfig;

    public function __construct()
    {
        $this->textoModel = new Texto();
        $this->siteConfig = new SiteConfig();
    }

    public function home(): void
    {
        $this->render('home', [
            'title' => 'Maurício de Oliveira | Escritor, Poeta e Pensador',
            'description' => 'Espaço literário do escritor Maurício de Oliveira. Explore poesias profundas, frases marcantes, crônicas cotidianas e pensamentos filosóficos.',
            'canonical' => APP_URL . '/',
            'activeTab' => 'home'
        ]);
    }

    public function poesias(): void
    {
        $textos = $this->textoModel->allPublic('poesias');
        $this->render('textos', [
            'title' => 'Poesias | Maurício de Oliveira',
            'description' => 'Coleção de poesias e poemas autorais do escritor Maurício de Oliveira. Versos que tocam a alma e inspiram sentimentos.',
            'canonical' => APP_URL . '/poesias',
            'activeTab' => 'poesias',
            'tipo' => 'poesias',
            'textos' => $textos
        ]);
    }

    public function frases(): void
    {
        $textos = $this->textoModel->allPublic('frases');
        $this->render('textos', [
            'title' => 'Frases e Aforismos | Maurício de Oliveira',
            'description' => 'Frases curtas, reflexões rápidas e aforismos marcantes de Maurício de Oliveira sobre a vida, o tempo e a condição humana.',
            'canonical' => APP_URL . '/frases',
            'activeTab' => 'frases',
            'tipo' => 'frases',
            'textos' => $textos,
            'showFrasesJoinhaIcon' => $this->siteConfig->showFrasesJoinhaIcon()
        ]);
    }

    public function cronicas(): void
    {
        $textos = $this->textoModel->allPublic('crônicas');
        $this->render('textos', [
            'title' => 'Crônicas | Maurício de Oliveira',
            'description' => 'Crônicas literárias e observações cotidianas escritas por Maurício de Oliveira. Histórias e análises sobre o dia a dia.',
            'canonical' => APP_URL . '/cronicas',
            'activeTab' => 'cronicas',
            'tipo' => 'crônicas',
            'textos' => $textos
        ]);
    }

    public function pensamentos(): void
    {
        $textos = $this->textoModel->allPublic('pensamentos');
        $this->render('textos', [
            'title' => 'Pensamentos e Reflexões | Maurício de Oliveira',
            'description' => 'Pensamentos profundos e reflexões intelectuais do escritor Maurício de Oliveira sobre sociedade, filosofia e psicologia.',
            'canonical' => APP_URL . '/pensamentos',
            'activeTab' => 'pensamentos',
            'tipo' => 'pensamentos',
            'textos' => $textos
        ]);
    }

    public function teorias(): void
    {
        $textos = $this->textoModel->allPublic('teorias');
        $this->render('textos', [
            'title' => 'Teorias | MaurÃ­cio de Oliveira',
            'description' => 'Teorias e ensaios reflexivos de MaurÃ­cio de Oliveira sobre pensamento, sociedade e conhecimento.',
            'canonical' => APP_URL . '/teorias',
            'activeTab' => 'teorias',
            'tipo' => 'teorias',
            'textos' => $textos
        ]);
    }

    public function sobre(): void
    {
        $this->render('sobre', [
            'title' => 'Sobre o Autor | Maurício de Oliveira',
            'description' => 'Conheça Maurício de Oliveira, escritor e poeta. Saiba mais sobre sua trajetória literária, inspirações, história e visão artística.',
            'canonical' => APP_URL . '/sobre',
            'activeTab' => 'sobre'
        ]);
    }

    public function joinha(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Texto inválido.']);
            return;
        }

        $texto = $this->textoModel->find($id);
        if (!$texto || ($texto['status'] ?? '') !== 'publicado') {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Texto não encontrado.']);
            return;
        }

        if (!$this->appendJoinhaLog($id)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Não foi possível registrar o joinha.']);
            return;
        }

        $count = $this->textoModel->incrementJoinhas($id);
        if ($count === null) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Texto não encontrado.']);
            return;
        }

        echo json_encode(['success' => true, 'count' => $count]);
    }

    private function appendJoinhaLog(int $id): bool
    {
        $storageDir = dirname(__DIR__, 2) . '/storage';
        if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true)) {
            return false;
        }

        $line = json_encode(['id' => $id], JSON_UNESCAPED_UNICODE) . PHP_EOL;
        return file_put_contents($storageDir . '/joinhas.json', $line, FILE_APPEND | LOCK_EX) !== false;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            require __DIR__ . '/../Views/layout.php';
        } else {
            http_response_code(500);
            echo "Erro: View '{$view}' não encontrada.";
        }
    }
}
