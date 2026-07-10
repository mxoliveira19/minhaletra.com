<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, callable|array $action): void
    {
        $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, callable|array $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => '/' . ltrim($path, '/'),
            'action' => $action
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH);
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $this->execute($route['action']);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        
        $title = "Página não encontrada";
        $description = "Página não encontrada no site do escritor Maurício de Oliveira.";
        
        // Define variable for layout
        $content = "
        <section class='card' style='text-align: center; padding: 60px 20px;'>
            <h1 style='font-size: 64px; margin-bottom: 20px; color: #74411f;'>404</h1>
            <p style='font-size: 20px; margin-bottom: 30px;'>Desculpe, a página que você procura não existe ou foi movida.</p>
            <a href='/' class='btn-nav' style='display: inline-block; padding: 12px 24px; background: #2a1d14; color: white; text-decoration: none; border-radius: 8px;'>Voltar ao Início</a>
        </section>";

        require_once __DIR__ . '/../../app/Views/layout.php';
    }

    private function execute(callable|array $action): void
    {
        if (is_callable($action)) {
            $action();
        } elseif (is_array($action)) {
            [$controllerClass, $method] = $action;
            $controller = new $controllerClass();
            $controller->$method();
        }
    }
}
