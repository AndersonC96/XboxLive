<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

class ProfileController extends BaseController
{
    private OpenXBLService $api;

    public function __construct()
    {
        if (!AuthService::check()) {
            $this->redirect('/login');
        }
        $this->api = new OpenXBLService();
    }

    public function index(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $xuid = $profile['profileUsers'][0]['xid'] ?? null;

        // Presence
        $presence = $this->api->getPresence($xuid);

        $this->render('perfil', [
            'title' => 'Meu Perfil - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'presence' => $presence
        ], 'main');
    }

    public function achievements(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $xuid = $profile['profileUsers'][0]['xid'] ?? null;

        $history = $this->api->getAchievementsV3($xuid);
        $titles = $history['titles'] ?? [];

        // Paginação (12 por página)
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($titles);
        $totalPages = ceil($totalItems / $perPage);
        $pagedTitles = array_slice($titles, ($page - 1) * $perPage, $perPage);

        $this->render('conquistas', [
            'title' => 'Minhas Conquistas - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'titles' => $pagedTitles,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function captures(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $xuid = $profile['profileUsers'][0]['xid'] ?? null;

        $screenshots = $this->api->getScreenshots($xuid) ?? [];
        $clips = $this->api->getGameClips($xuid) ?? [];

        $this->render('capturas', [
            'title' => 'Minhas Capturas - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'screenshots' => $screenshots,
            'clips' => $clips
        ], 'main');
    }

    public function search(): void
    {
        $query = $_GET['gamertag_search'] ?? '';
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $results = [];
        if (!empty($query)) {
            $results = $this->api->searchGamertag($query);
        }

        $this->render('search', [
            'title' => 'Buscar Jogadores - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'query' => $query,
            'results' => $results['people'] ?? []
        ], 'main');
    }
}
