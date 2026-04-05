<?php

require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        header('Location: ../pages/register.php?error=emptyfields');
        exit();
    }

    if (AuthService::register($username, $email, $password)) {
        // Logar automaticamente após o registro
        AuthService::login($username, $password);
        header('Location: ../pages/dashboard.php');
        exit();
    } else {
        header('Location: ../pages/register.php?error=regfailed');
        exit();
    }
} else {
    header('Location: ../pages/register.php');
    exit();
}