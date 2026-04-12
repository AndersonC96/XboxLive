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
                    FlashMessage::set('error', "Xbox API Error: " . $e->getMessage());
                    // Redirecionamento seguro para dashboard em caso de falha de API
                    header("Location: " . $basePath . "/dashboard");
                    exit();
                } catch (\Throwable $e) {
                    // Log do erro real (simulado aqui, poderia usar error_log)
                    error_log($e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
                    $this->renderError(500, "Erro Interno do Servidor", $e->getMessage());
                }
                return;
            }
        }

        $this->renderError(404, "Página Não Encontrada", "O conteúdo que você busca não existe.");
    }

    private function renderError(int $code, string $title, string $message): void
    {
        http_response_code($code);
        
        $baseUrl = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        if ($baseUrl === '/') $baseUrl = '';

        // Em produção, não expor a mensagem real se for 500
        $displayMessage = ($code === 500 && !str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost')) 
            ? "Ocorreu um erro inesperado. Tente novamente mais tarde." 
            : $message;

        $viewPath = __DIR__ . '/../../views/errors/' . $code . '.php';
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . '/../../views/errors/404.php'; // Fallback
        }

        $layoutPath = __DIR__ . '/../../views/layouts/main.php';
        
        $userProfile = null;
        $showNavbar = AuthService::check();

        ob_start();
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>$title</h1><p>$displayMessage</p>";
        }
        $content = ob_get_clean();

        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }
}
