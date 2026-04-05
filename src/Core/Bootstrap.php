<?php

namespace Anderson\XboxLive\Core;

class Bootstrap
{
    public static function run()
    {
        // Iniciar sessão se ainda não começou
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Inicializar configurações
        Config::init();
    }
}
