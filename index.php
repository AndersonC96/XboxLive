<?php

require_once __DIR__ . '/vendor/autoload.php';

use Anderson\XboxLive\Core\Bootstrap;
use Anderson\XboxLive\Router\Router;
use Anderson\XboxLive\Controllers\AuthController;
use Anderson\XboxLive\Controllers\DashboardController;
use Anderson\XboxLive\Controllers\GamePassController;
use Anderson\XboxLive\Controllers\ProfileController;
use Anderson\XboxLive\Controllers\StoreController;

Bootstrap::run();

$router = new Router();

// Rotas de Autenticação
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

// Rotas da Dashboard e Social
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/amigos', [DashboardController::class, 'friends']);
$router->get('/seguidores', [DashboardController::class, 'followers']);
$router->get('/feed', [DashboardController::class, 'activityFeed']);
$router->get('/recentes', [DashboardController::class, 'recentPlayers']);
$router->get('/bloqueados', [DashboardController::class, 'blocks']);

// Rotas de Perfil e Atividades
$router->get('/perfil', [ProfileController::class, 'index']);
$router->get('/conquistas', [ProfileController::class, 'achievements']);
$router->get('/capturas', [ProfileController::class, 'captures']);
$router->get('/search', [ProfileController::class, 'search']);

// Rotas Game Pass e Loja
$router->get('/todos_os_jogos', [GamePassController::class, 'allGames']);
$router->get('/todos_os_jogos/sync', [GamePassController::class, 'sync']);
$router->get('/ea_play', [GamePassController::class, 'eaPlay']);
$router->get('/gamepass_pc', [GamePassController::class, 'pcGamePass']);
$router->get('/jogo', [GamePassController::class, 'gameDetails']);

// Rotas da Loja (Marketplace)
$router->get('/mais_jogados', [StoreController::class, 'mostPlayed']);
$router->get('/promocao', [StoreController::class, 'deals']);
$router->get('/novos_jogos', [StoreController::class, 'newGames']);

$router->resolve();
