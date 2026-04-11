<?php

require_once __DIR__ . '/vendor/autoload.php';

use Anderson\XboxLive\Core\Bootstrap;
use Anderson\XboxLive\Router\Router;
use Anderson\XboxLive\Controllers\AuthController;
use Anderson\XboxLive\Controllers\DashboardController;

Bootstrap::run();

$router = new Router();

// Rotas de Autenticação
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

// Rotas da Dashboard
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->resolve();
