<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Database;

class AuthService
{
    public static function login(string $username, string $password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Prevenção de fixação de sessão
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['xuid'] = $user['xuid'] ?? null;
            return true;
        }

        return false;
    }

    public static function register(string $username, string $email, string $password): bool
    {
        $db = Database::getInstance();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            return $stmt->execute([
                'username' => $username,
                'email'    => $email,
                'password' => $hash
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function updateXuid(int $userId, string $xuid): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET xuid = :xuid WHERE id = :id");
        if ($stmt->execute(['id' => $userId, 'xuid' => $xuid])) {
            $_SESSION['xuid'] = $xuid;
            return true;
        }
        return false;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): array
    {
        return [
            'id'       => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email'    => $_SESSION['email'] ?? null,
            'xuid'     => $_SESSION['xuid'] ?? null,
        ];
    }

    public static function logout(): void
    {
        // Limpa array de sessão
        $_SESSION = [];
        
        // Invalida o cookie de sessão no navegador
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destrói a sessão no servidor
        session_destroy();
    }
}
