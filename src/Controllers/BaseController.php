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

    protected function getUserProfileStats(?array $profileData): array
    {
        $sessionUser = \Anderson\XboxLive\Services\AuthService::user();
        
        $stats = [
            'gamertag'   => $sessionUser['username'] ?? 'Usuário',
            'gamerpic'   => 'img/default_avatar.jpg',
            'gamerscore' => '0',
            'tier'       => 'Silver',
            'reputation' => 'Good',
            'bio'        => 'Nenhuma bio disponível.',
            'location'   => 'Não informada'
        ];

        if ($profileData && isset($profileData['settings'])) {
            foreach ($profileData['settings'] as $setting) {
                switch ($setting['id']) {
                    case 'Gamertag': $stats['gamertag'] = $setting['value']; break;
                    case 'GameDisplayPicRaw': $stats['gamerpic'] = $setting['value']; break;
                    case 'Gamerscore': $stats['gamerscore'] = number_format($setting['value'], 0, ',', '.'); break;
                    case 'AccountTier': $stats['tier'] = $setting['value']; break;
                    case 'XboxOneRep': $stats['reputation'] = $setting['value']; break;
                    case 'Bio': $stats['bio'] = $setting['value']; break;
                    case 'Location': $stats['location'] = $setting['value']; break;
                }
            }
        }
        return $stats;
    }
}
