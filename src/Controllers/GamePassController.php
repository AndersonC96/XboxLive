<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;
use Anderson\XboxLive\Core\Database;
use PDO;

class GamePassController extends BaseController
{
    private OpenXBLService $api;

    public function __construct()
    {
        if (!AuthService::check()) {
            $this->redirect('/login');
        }
        $this->api = new OpenXBLService();
    }

    private function getPagedGames(string $tableName): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT game_id FROM $tableName");
        $stmt->execute();
        $allGameIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($allGameIds);
        $totalPages = ceil($totalItems / $perPage);
        $currentPageIds = array_slice($allGameIds, ($page - 1) * $perPage, $perPage);

        $games = [];
        if (!empty($currentPageIds)) {
            $details = $this->api->getProductDetails($currentPageIds);
            $games = $details['Products'] ?? [];
        }

        return [$games, $page, $totalPages];
    }

    private function syncTable(string $tableName, $apiResponse, string $redirectPath)
    {
        $db = Database::getInstance();
        if ($apiResponse && is_array($apiResponse)) {
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM $tableName WHERE game_id = :game_id");
            $insertStmt = $db->prepare("INSERT INTO $tableName (game_id) VALUES (:game_id)");
            
            $added = 0;
            foreach ($apiResponse as $game) {
                if (isset($game['id'])) {
                    $gameId = $game['id'];
                    $checkStmt->execute(['game_id' => $gameId]);
                    if (!$checkStmt->fetchColumn()) {
                        $insertStmt->execute(['game_id' => $gameId]);
                        $added++;
                    }
                }
            }
            $this->redirect($redirectPath . '?synced=' . $added);
        } else {
            $this->redirect($redirectPath . '?error=sync_failed');
        }
    }

    public function allGames(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('gamepass_games');

        $this->render('todos_os_jogos', [
            'title' => 'Xbox Game Pass - Catálogo',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function sync(): void
    {
        $this->syncTable('gamepass_games', $this->api->getGamePassAll(), '/todos_os_jogos');
    }

    public function eaPlay(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('ea_gamepass');

        $this->render('ea_play', [
            'title' => 'EA Play - Catálogo',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function syncEAPlay(): void
    {
        $this->syncTable('ea_gamepass', $this->api->getEAPlayAll(), '/ea_play');
    }

    public function pcGamePass(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('pc_gamepass');

        $this->render('gamepass_pc', [
            'title' => 'PC Game Pass - Catálogo',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function syncPCGamePass(): void
    {
        $this->syncTable('pc_gamepass', $this->api->getPCGamePassAll(), '/gamepass_pc');
    }

    public function noController(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('jogos_sem_controle');

        $this->render('jogos_sem_controle', [
            'title' => 'Jogos Sem Controle - Xbox Cloud',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function syncNoController(): void
    {
        $this->syncTable('jogos_sem_controle', $this->api->getNoControllerGames(), '/jogos_sem_controle');
    }

    // --- Novas Seções ---

    public function addedRecently(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('gamepass_novos');

        $this->render('gamepass_novos', [
            'title' => 'Adicionados Recentemente - Game Pass',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function syncAddedRecently(): void
    {
        $this->syncTable('gamepass_novos', $this->api->getNewGamePass(), '/adicionados_recentemente');
    }

    public function comingSoon(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('gamepass_em_breve');

        $this->render('gamepass_em_breve', [
            'title' => 'Em Breve - Game Pass',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function syncComingSoon(): void
    {
        $this->syncTable('gamepass_em_breve', $this->api->getComingSoon(), '/em_breve');
    }

    public function leavingSoon(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('gamepass_saindo');

        $this->render('gamepass_saindo', [
            'title' => 'Saindo em Breve - Game Pass',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function syncLeavingSoon(): void
    {
        $this->syncTable('gamepass_saindo', $this->api->getLeavingSoon(), '/saindo_em_breve');
    }

    public function gameDetails(): void
    {
        $idParam = $_GET['id'] ?? null;
        if (!$idParam) {
            $this->redirect('/todos_os_jogos');
        }

        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $xuid = $profile['profileUsers'][0]['id'] ?? null;

        $productId = $idParam;
        $historyTitle = null;

        if (is_numeric($idParam)) {
            $history = $this->api->get("player/titleHistory");
            $titles = $history['titles'] ?? [];
            foreach ($titles as $t) {
                if (($t['titleId'] ?? '') == $idParam) {
                    $historyTitle = $t;
                    if (!empty($t['productId'])) {
                        $productId = $t['productId'];
                    }
                    break;
                }
            }
        }

        $response = $this->api->getProductDetails($productId);
        $product = $response['Products'][0] ?? null;

        $statsData = [];
        if ($product && $xuid) {
            $statsResponse = $this->api->get("player/stats/$productId/$xuid");
            $statsData = $statsResponse['groups'][0]['stats'] ?? [];
        }

        $this->render('jogo', [
            'title' => ($product['LocalizedProperties'][0]['ProductTitle'] ?? 'Detalhes do Jogo') . ' - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'product' => $product,
            'historyTitle' => $historyTitle,
            'statsData' => $statsData,
            'productId' => $productId
        ], 'main');
    }
}
