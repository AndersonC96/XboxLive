<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

class ProfileController extends BaseController
{
    private OpenXBLService $api;

    public function __construct()
    {
        $this->api = new OpenXBLService();
    }

    public function index(): void
    {
        $profile = $this->api->getAccount();
        $profileData = $profile['profileUsers'][0] ?? null;
        $userProfile = $this->getUserProfileStats($profileData);
        
        // No OpenXBL v2, o ID costuma ser 'id' ou 'hostId'
        $xuid = $profileData['id'] ?? ($profileData['hostId'] ?? null);

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
        $profileData = $profile['profileUsers'][0] ?? null;
        $userProfile = $this->getUserProfileStats($profileData);
        
        $xuid = $profileData['id'] ?? ($profileData['hostId'] ?? null);

        $titles = [];
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalPages = 1;

        // Utilizamos getTitleHistory que é mais robusto para listagem de jogos
        $history = $this->api->getTitleHistory($xuid);
        
        if ($history) {
            $titles = $history['titles'] ?? [];
            
            // Lógica de busca (opcional, se desejar manter)
            $search = $_GET['q'] ?? '';
            if (!empty($search)) {
                $titles = array_filter($titles, function($t) use ($search) {
                    return stripos($t['name'] ?? '', $search) !== false;
                });
            }

            $totalItems = count($titles);
            $totalPages = max(1, ceil($totalItems / $perPage));
            $titles = array_slice($titles, ($page - 1) * $perPage, $perPage);
        }

        $this->render('conquistas', [
            'title' => 'Minhas Conquistas - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'titles' => $titles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $_GET['q'] ?? ''
        ], 'main');
    }

    public function captures(): void
    {
        $profile = $this->api->getAccount();
        $profileData = $profile['profileUsers'][0] ?? null;
        $userProfile = $this->getUserProfileStats($profileData);
        $xuid = $profileData['id'] ?? ($profileData['hostId'] ?? null);

        $screenshots = $this->api->getScreenshots($xuid) ?? [];
        $clips = $this->api->getGameClips($xuid) ?? [];

        $this->render('capturas', [
            'title' => 'Minhas Capturas - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'screenshots' => $screenshots['screenshots'] ?? ($screenshots ?: []),
            'clips' => $clips['gameClips'] ?? ($clips ?: [])
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

    public function titleAchievements(): void
    {
        $titleId = $_GET['titleId'] ?? null;
        if (!$titleId) $this->redirect('/conquistas');

        $profile = $this->api->getAccount();
        $profileData = $profile['profileUsers'][0] ?? null;
        $userProfile = $this->getUserProfileStats($profileData);
        $xuid = $profileData['id'] ?? ($profileData['hostId'] ?? null);

        $achievements = $this->api->getAchievementsForTitle($titleId, $xuid);

        $this->render('conquistas_jogo', [
            'title' => 'Conquistas do Jogo - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'achievements' => $achievements['achievements'] ?? []
        ], 'main');
    }
}
