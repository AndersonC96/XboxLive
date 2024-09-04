<?php
    session_start();
    require '../config/db.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        if (empty($username) || empty($password)) {
            header('Location: ../pages/login.php?error=emptyfields');
            exit();
        }
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
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