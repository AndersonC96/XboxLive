<?php

require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header('Location: ../pages/login.php?error=emptyfields');
        exit();
    }

    if (AuthService::login($username, $password)) {
        header('Location: ../pages/dashboard.php');
        exit();
    } else {
        header('Location: ../pages/login.php?error=wrongcredentials');
        exit();
    }
} else {
    header('Location: ../pages/login.php');
    exit();
}