<?php

namespace Anderson\XboxLive\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host = Config::get('DB_HOST', 'localhost');
        $db   = Config::get('DB_NAME', 'xboxlive_dashboard');
        $user = Config::get('DB_USER', 'root');
        $pass = Config::get('DB_PASSWORD') ?: Config::get('DB_PASS', '');
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage() . "<br><br><b>Dica:</b> Certifique-se de que o banco de dados '{$db}' existe e que o arquivo <b>.env</b> na raiz está configurado corretamente.");
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
