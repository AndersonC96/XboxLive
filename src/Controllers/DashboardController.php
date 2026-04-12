<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

class DashboardController extends BaseController
{
    private OpenXBLService $api;

    public function __construct()
    {
        $this->api = new OpenXBLService();
    }

    public function index(): void
    {
        // Fetch Profile
        $profile = $this->api->getAccount();
        $profileData = $profile['profileUsers'][0] ?? null;

        // Fetch Presence
        $presence = $this->api->getPlayerSummary();
        $presenceState = $presence['people'][0]['presenceState'] ?? 'Offline';
        $presenceText = $presence['people'][0]['presenceText'] ?? 'N/A';

        // Fetch History
        $history = $this->api->get("player/titleHistory");
        $recentTitles = isset($history['titles']) ? array_slice($history['titles'], 0, 4) : [];

        // Use centralized method for profile stats
        $stats = $this->getUserProfileStats($profileData);

        $this->render('dashboard', [
            'title' => 'Dashboard - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $stats,
            'presenceState' => $presenceState,
            'presenceText' => $presenceText,
            'recentTitles' => $recentTitles
        ], 'main');
    }

    public function friends(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $friendsData = $this->api->getFriends();
        $allFriends = $friendsData['people'] ?? [];
        
        // Paginação
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($allFriends);
        $totalPages = ceil($totalItems / $perPage);
        $friends = array_slice($allFriends, ($page - 1) * $perPage, $perPage);

        $this->render('amigos', [
            'title' => 'Meus Amigos - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'friends' => $friends,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function followers(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $followersData = $this->api->getFollowers();
        $allFollowers = $followersData['people'] ?? [];

        // Paginação
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($allFollowers);
        $totalPages = ceil($totalItems / $perPage);
        $followers = array_slice($allFollowers, ($page - 1) * $perPage, $perPage);

        $this->render('seguidores', [
            'title' => 'Meus Seguidores - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'followers' => $followers,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function activityFeed(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        // Fetch Social Feed
        $feedData = $this->api->getActivityFeed();
        $allActivities = $feedData['activityItems'] ?? [];

        // Paginação
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($allActivities);
        $totalPages = ceil($totalItems / $perPage);
        $activities = array_slice($allActivities, ($page - 1) * $perPage, $perPage);

        $this->render('feed', [
            'title' => 'Feed de Atividade - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'activities' => $activities,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function recentPlayers(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $recentData = $this->api->getRecentPlayers();
        $allPlayers = $recentData['people'] ?? [];

        // Process and enrich player data
        $processedPlayers = array_map(function($player) {
            $recentInfo = $player['recentPlayer'] ?? null;
            $lastTitle = $recentInfo['titles'][0] ?? null;
            
            return [
                'xuid' => $player['xuid'] ?? '',
                'gamertag' => $player['gamertag'] ?? 'Unknown',
                'displayPicRaw' => $player['displayPicRaw'] ?? 'img/default_avatar.jpg',
                'presenceState' => $player['presenceState'] ?? 'Offline',
                'encounterGame' => $lastTitle['titleName'] ?? 'Jogo Desconhecido',
                'encounterText' => $recentInfo['text'] ?? 'Sem detalhes do encontro',
                'isCodHq' => ($lastTitle['titleId'] ?? '') === '2001700854'
            ];
        }, $allPlayers);

        // Paginação
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($processedPlayers);
        $totalPages = ceil($totalItems / $perPage);
        $players = array_slice($processedPlayers, ($page - 1) * $perPage, $perPage);

        $this->render('recentes', [
            'title' => 'Jogadores Recentes - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'players' => $players,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'activePage' => 'recentes'
        ], 'main');
    }

    public function blocks(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $blocksData = $this->api->get("friends/mute"); 
        $allBlocks = $blocksData['people'] ?? [];

        // Paginação
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalItems = count($allBlocks);
        $totalPages = ceil($totalItems / $perPage);
        $blocks = array_slice($allBlocks, ($page - 1) * $perPage, $perPage);

        $this->render('bloqueados', [
            'title' => 'Jogadores Bloqueados - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'blocks' => $blocks,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }
}
