<?php

namespace Anderson\XboxLive\Router;

use Anderson\XboxLive\Services\AuthService;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler, bool $protected = false): void
    {
        $this->addRoute('GET', $path, $handler, $protected);
    }

    public function post(string $path, $handler, bool $protected = false): void
    {
        $this->addRoute('POST', $path, $handler, $protected);
    }

    private function addRoute(string $method, string $path, $handler, bool $protected): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'protected' => $protected
        ];
    }

    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $path = str_replace($basePath, '', $path);
        $path = $path === '' ? '/' : $path;

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                
                // Middleware Simples: Proteção de Rota
                if ($route['protected'] && !AuthService::check()) {
                    header("Location: " . $basePath . "/login");
                    exit();
                }

                $handler = $route['handler'];
                
                if (is_array($handler)) {
                    [$controllerClass, $methodName] = $handler;
                    $controller = new $controllerClass();
                    $controller->$methodName();
                } else {
                    $handler();
                }
                return;
            }
        }

        // Renderizar 404 Customizada
        $this->renderNotFound();
    }

    private function renderNotFound(): void
    {
        http_response_code(404);
        
        // Criamos uma mini-implementação de render para o 404 aqui para ser independente
        $content = "Erro 404";
        $viewPath = __DIR__ . '/../../views/errors/404.php';
        $layoutPath = __DIR__ . '/../../views/layouts/main.php';
        
        $userProfile = null; // 404 pode não ter perfil
        $showNavbar = AuthService::check();

        if (file_exists($viewPath)) {
            ob_start();
            include $viewPath;
            $content = ob_get_clean();
        }

        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }
}
