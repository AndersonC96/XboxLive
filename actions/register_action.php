<?php
    require '../config/db.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            header('Location: ../pages/register.php?error=emptyfields');
            exit();
        }
        if ($password !== $confirm_password) {
            header('Location: ../pages/register.php?error=passwordmismatch');
            exit();
        }
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->rowCount() > 0) {
            header('Location: ../pages/register.php?error=userexists');
            exit();
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password
        ]);
        header('Location: ../pages/login.php?success=registered');
        exit();
    } else {
        header('Location: ../pages/register.php');
        exit();
    }