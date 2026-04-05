<?php

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Database;

class AuthService
{
    public static function login($username, $password)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            return true;
        }

        return false;
    }

    public static function register($username, $email, $password)
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

    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function user()
    {
        return [
            'id'       => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email'    => $_SESSION['email'] ?? null,
        ];
    }

    public static function logout()
    {
        session_destroy();
    }
}
