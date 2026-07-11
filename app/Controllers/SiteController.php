<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Texto;

final class SiteController
{
    private Texto $textoModel;

    public function __construct()
    {
        $this->textoModel = new Texto();
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
            'textos' => $textos
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
