<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Router;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Helpers\FlashMessage;
use Anderson\XboxLive\Exceptions\XblApiException;

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
                
                if ($route['protected'] && !AuthService::check()) {
                    header("Location: " . $basePath . "/login");
                    exit();
                }

                try {
                    $handler = $route['handler'];
                    if (is_array($handler)) {
                        [$controllerClass, $methodName] = $handler;
                        $controller = new $controllerClass();
                        $controller->$methodName();
                    } else {
                        $handler();
                    }
                } catch (XblApiException $e) {
                    FlashMessage::set('error', $e->getMessage());
                    // Se der erro de API na dashboard, tenta renderizar com o que tem
                    header("Location: " . $_SERVER['HTTP_REFERER'] ?? ($basePath . '/dashboard'));
                } catch (\Exception $e) {
                    $this->renderError($e->getMessage());
                }
                return;
            }
        }

        $this->renderNotFound();
    }

    private function renderNotFound(): void
    {
        $this->renderCustomError("404 - Página Não Encontrada", "O conteúdo solicitado não existe.", 404);
    }

    private function renderError(string $message): void
    {
        $this->renderCustomError("Erro de Sistema", $message, 500);
    }

    private function renderCustomError(string $title, string $message, int $code): void
    {
        http_response_code($code);
        $baseUrl = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        if ($baseUrl === '/') $baseUrl = '';

        $viewPath = __DIR__ . '/../../views/errors/404.php';
        $layoutPath = __DIR__ . '/../../views/layouts/main.php';
        
        $userProfile = null;
        $showNavbar = AuthService::check();

        ob_start();
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>$title</h1><p>$message</p>";
        }
        $content = ob_get_clean();

        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }
}
