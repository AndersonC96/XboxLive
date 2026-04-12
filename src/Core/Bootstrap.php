<?php

namespace Anderson\XboxLive\Core;

class Bootstrap
{
    public static function run()
    {
        // Configurações de segurança de sessão
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_samesite', 'Lax');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Config::init();
    }
}
