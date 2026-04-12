<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;
use Anderson\XboxLive\Core\Database;
use PDO;

class StoreController extends BaseController
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
        $perPage = 18;
        $totalItems = count($allGameIds);
        $totalPages = max(1, ceil($totalItems / $perPage));
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
        
        $items = $apiResponse['productSummaries'] ?? ($apiResponse['items'] ?? []);
        
        if (!empty($items) && is_array($items)) {
            $db->exec("TRUNCATE TABLE $tableName");
            $insertStmt = $db->prepare("INSERT INTO $tableName (game_id) VALUES (:game_id)");
            
            $added = 0;
            foreach ($items as $item) {
                $gameId = $item['productId'] ?? ($item['id'] ?? null);
                if ($gameId) {
                    $insertStmt->execute(['game_id' => $gameId]);
                    $added++;
                }
            }
            $this->redirect($redirectPath . '?synced=' . $added);
        } else {
            $this->redirect($redirectPath . '?error=sync_failed');
        }
    }

    public function storeHome(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getStoreHome();

        $this->render('store_home', [
            'title' => 'Loja Xbox - Início',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'collections' => $data ?? []
        ], 'main');
    }

    public function mostPlayed(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_most_played');

        $this->render('mais_jogados', [
            'title' => 'Mais Jogados - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/mais_jogados/sync'
        ], 'main');
    }

    public function syncMostPlayed(): void
    {
        $this->syncTable('marketplace_most_played', $this->api->getMostPlayed(), '/mais_jogados');
    }

    public function deals(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_deals');

        $this->render('promocao', [
            'title' => 'Promoções - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/promocao/sync'
        ], 'main');
    }

    public function syncDeals(): void
    {
        $this->syncTable('marketplace_deals', $this->api->getDeals(), '/promocao');
    }

    public function newGames(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_new');

        $this->render('novos_jogos', [
            'title' => 'Novos Jogos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/novos_jogos/sync'
        ], 'main');
    }

    public function syncNew(): void
    {
        $this->syncTable('marketplace_new', $this->api->getMarketplaceNew(), '/novos_jogos');
    }

    public function topPaid(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_top_paid');

        $this->render('mais_jogados', [
            'title' => 'Top Pagos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/top_pagos/sync'
        ], 'main');
    }

    public function syncTopPaid(): void
    {
        $this->syncTable('marketplace_top_paid', $this->api->getTopPaid(), '/top_pagos');
    }

    public function topFree(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_top_free');

        $this->render('mais_jogados', [
            'title' => 'Top Gratuitos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/top_gratis/sync'
        ], 'main');
    }

    public function syncTopFree(): void
    {
        $this->syncTable('marketplace_top_free', $this->api->getTopFree(), '/top_gratis');
    }

    public function bestRated(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_best_rated');

        $this->render('mais_jogados', [
            'title' => 'Melhores Avaliados - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/melhores_avaliados/sync'
        ], 'main');
    }

    public function syncBestRated(): void
    {
        $this->syncTable('marketplace_best_rated', $this->api->getBestRated(), '/melhores_avaliados');
    }

    public function comingSoon(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        [$games, $page, $totalPages] = $this->getPagedGames('marketplace_coming_soon');

        $this->render('novos_jogos', [
            'title' => 'Chegando em Breve - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'syncUrl' => '/chegando_em_breve/sync'
        ], 'main');
    }

    public function syncComingSoon(): void
    {
        $this->syncTable('marketplace_coming_soon', $this->api->getMarketplaceComingSoon(), '/chegando_em_breve');
    }
}
