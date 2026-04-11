<?php

namespace Anderson\XboxLive\Router;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Ajuste para rodar em subdiretórios (como no XAMPP)
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $path = str_replace($basePath, '', $path);
        $path = $path === '' ? '/' : $path;

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
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

        http_response_code(404);
        echo "404 - Página não encontrada";
    }

    private function matchPath(string $routePath, string $requestPath): bool
    {
        return $routePath === $requestPath;
    }
}
