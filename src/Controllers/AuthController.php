<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Helpers\FlashMessage;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (AuthService::check()) {
            $this->redirect('/dashboard');
        }

        $this->render('login', [
            'title' => 'Login - Xbox Live Dashboard',
            'error' => $_GET['error'] ?? null
        ], 'main');
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->redirect('/login?error=emptyfields');
        }

        if (AuthService::login($username, $password)) {
            $this->redirect('/dashboard');
        } else {
            $this->redirect('/login?error=wrongcredentials');
        }
    }

    public function showRegister(): void
    {
        if (AuthService::check()) {
            $this->redirect('/dashboard');
        }

        $this->render('register', [
            'title' => 'Criar Conta - Xbox Live Dashboard',
            'error' => $_GET['error'] ?? null
        ], 'main');
    }

    public function register(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($email) || empty($password)) {
            $this->redirect('/register?error=emptyfields');
        }

        if (AuthService::register($username, $email, $password)) {
            $this->redirect('/login?success=registered');
        } else {
            $this->redirect('/register?error=registrationfailed');
        }
    }

    public function logout(): void
    {
        AuthService::logout();
        $this->redirect('/login');
    }
}
