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

    public function allGames(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        // 1. Buscar IDs do banco de dados
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT game_id FROM gamepass_games");
        $stmt->execute();
        $allGameIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 2. Paginação
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($allGameIds);
        $totalPages = ceil($totalItems / $perPage);
        $currentPageIds = array_slice($allGameIds, ($page - 1) * $perPage, $perPage);

        // 3. Buscar detalhes dos jogos da página atual
        $games = [];
        if (!empty($currentPageIds)) {
            $details = $this->api->getProductDetails($currentPageIds);
            $games = $details['Products'] ?? [];
        }

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
        $db = Database::getInstance();
        $response = $this->api->getGamePassAll();

        if ($response && is_array($response)) {
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM gamepass_games WHERE game_id = :game_id");
            $insertStmt = $db->prepare("INSERT INTO gamepass_games (game_id) VALUES (:game_id)");
            
            $added = 0;
            foreach ($response as $game) {
                if (isset($game['id'])) {
                    $gameId = $game['id'];
                    $checkStmt->execute(['game_id' => $gameId]);
                    if (!$checkStmt->fetchColumn()) {
                        $insertStmt->execute(['game_id' => $gameId]);
                        $added++;
                    }
                }
            }
            // Redireciona de volta com mensagem de sucesso (opcional)
            $this->redirect('/todos_os_jogos?synced=' . $added);
        } else {
            $this->redirect('/todos_os_jogos?error=sync_failed');
        }
    }

    public function eaPlay(): void
    {
        // Mesma lógica poderia ser aplicada filtrando IDs específicos de EA
        $this->allGames(); 
    }

    public function pcGamePass(): void
    {
        $this->allGames();
    }

    public function gameDetails(): void
    {
        $idParam = $_GET['id'] ?? null;
        if (!$idParam) {
            $this->redirect('/todos_os_jogos');
        }

        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $xuid = $profile['profileUsers'][0]['xid'] ?? null;

        $productId = $idParam;
        $historyTitle = null;

        // Se o ID for numérico (TitleId), tentamos resolver para ProductId
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

        // Detalhes do Marketplace
        $response = $this->api->getProductDetails($productId);
        $product = $response['Products'][0] ?? null;

        // Estatísticas (opcional conforme suporte da API)
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
