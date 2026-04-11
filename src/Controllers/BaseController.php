<?php

namespace Anderson\XboxLive\Controllers;

class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        
        // Caminho absoluto para a raiz das views
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../../views/layouts/' . $layout . '.php';

        if (!file_exists($viewPath)) {
            die("Erro: View '{$view}' não encontrada em {$viewPath}.");
        }

        // Buffer do conteúdo da página
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }

    protected function redirect(string $path): void
    {
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        header("Location: " . $basePath . $path);
        exit();
    }
}
