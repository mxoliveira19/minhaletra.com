<?php

declare(strict_types=1);

// Custom PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../app/Config/config.php';

use App\Core\Router;
use App\Controllers\SiteController;
use App\Controllers\AdminController;

// Initialize Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();

// Public routes
$router->get('/', [SiteController::class, 'home']);
$router->get('/poesias', [SiteController::class, 'poesias']);
$router->get('/frases', [SiteController::class, 'frases']);
$router->get('/cronicas', [SiteController::class, 'cronicas']);
$router->get('/pensamentos', [SiteController::class, 'pensamentos']);
$router->get('/teorias', [SiteController::class, 'teorias']);
$router->get('/sobre', [SiteController::class, 'sobre']);

// Admin routes
$router->get('/admin/login', [AdminController::class, 'login']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->get('/admin/logout', [AdminController::class, 'logout']);

$router->get('/admin', [AdminController::class, 'dashboard']);
$router->post('/admin/novo', [AdminController::class, 'novo']);
$router->post('/admin/editar', [AdminController::class, 'editar']);
$router->get('/admin/status', [AdminController::class, 'status']);
$router->post('/admin/status', [AdminController::class, 'status']);
$router->get('/admin/deletar', [AdminController::class, 'deletar']);
$router->post('/admin/deletar', [AdminController::class, 'deletar']);
$router->get('/admin/credenciais', [AdminController::class, 'credenciais']);
$router->post('/admin/credenciais', [AdminController::class, 'credenciais']);

// Dispatch
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
