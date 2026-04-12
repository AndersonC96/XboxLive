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

// Rotas Públicas
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Rotas Protegidas (true no final)
$router->get('/', [DashboardController::class, 'index'], true);
$router->get('/dashboard', [DashboardController::class, 'index'], true);
$router->get('/amigos', [DashboardController::class, 'friends'], true);
$router->get('/seguidores', [DashboardController::class, 'followers'], true);
$router->get('/feed', [DashboardController::class, 'activityFeed'], true);
$router->get('/recentes', [DashboardController::class, 'recentPlayers'], true);
$router->get('/bloqueados', [DashboardController::class, 'blocks'], true);

$router->get('/perfil', [ProfileController::class, 'index'], true);
$router->get('/conquistas', [ProfileController::class, 'achievements'], true);
$router->get('/conquistas/jogo', [ProfileController::class, 'titleAchievements'], true);
$router->get('/capturas', [ProfileController::class, 'captures'], true);
$router->get('/search', [ProfileController::class, 'search'], true);

$router->get('/todos_os_jogos', [GamePassController::class, 'allGames'], true);
$router->get('/todos_os_jogos/sync', [GamePassController::class, 'sync'], true);
$router->get('/adicionados_recentemente', [GamePassController::class, 'addedRecently'], true);
$router->get('/adicionados_recentemente/sync', [GamePassController::class, 'syncAddedRecently'], true);
$router->get('/em_breve', [GamePassController::class, 'comingSoon'], true);
$router->get('/em_breve/sync', [GamePassController::class, 'syncComingSoon'], true);
$router->get('/saindo_em_breve', [GamePassController::class, 'leavingSoon'], true);
$router->get('/saindo_em_breve/sync', [GamePassController::class, 'syncLeavingSoon'], true);
$router->get('/ea_play', [GamePassController::class, 'eaPlay'], true);
$router->get('/ea_play/sync', [GamePassController::class, 'syncEAPlay'], true);
$router->get('/gamepass_pc', [GamePassController::class, 'pcGamePass'], true);
$router->get('/gamepass_pc/sync', [GamePassController::class, 'syncPCGamePass'], true);
$router->get('/jogos_sem_controle', [GamePassController::class, 'noController'], true);
$router->get('/jogos_sem_controle/sync', [GamePassController::class, 'syncNoController'], true);
$router->get('/jogo', [GamePassController::class, 'gameDetails'], true);

$router->get('/store', [StoreController::class, 'storeHome'], true);
$router->get('/mais_jogados', [StoreController::class, 'mostPlayed'], true);
$router->get('/promocao', [StoreController::class, 'deals'], true);
$router->get('/novos_jogos', [StoreController::class, 'newGames'], true);
$router->get('/top_pagos', [StoreController::class, 'topPaid'], true);
$router->get('/top_gratis', [StoreController::class, 'topFree'], true);
$router->get('/melhores_avaliados', [StoreController::class, 'bestRated'], true);
$router->get('/chegando_em_breve', [StoreController::class, 'comingSoon'], true);

$router->resolve();
