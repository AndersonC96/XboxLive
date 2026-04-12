<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Controllers;

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
        $userProfile = $this->api->getProfileDTO();
        
        $presence = $this->api->getPresence($userProfile->xuid);
        $presenceState = $presence['people'][0]['presenceState'] ?? 'Offline';
        $presenceText = $presence['people'][0]['presenceText'] ?? 'N/A';

        $history = $this->api->getTitleHistory($userProfile->xuid);
        $recentTitles = isset($history['titles']) ? array_slice($history['titles'], 0, 4) : [];

        $this->render('dashboard', [
            'title' => 'Dashboard - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'presenceState' => $presenceState,
            'presenceText' => $presenceText,
            'recentTitles' => $recentTitles
        ], 'main');
    }

    public function friends(): void
    {
        $userProfile = $this->api->getProfileDTO();
        $friendsData = $this->api->getFriends();
        $allFriends = $friendsData['people'] ?? [];
        
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalPages = (int)ceil(count($allFriends) / $perPage);
        $friends = array_slice($allFriends, ($page - 1) * $perPage, $perPage);

        $this->render('amigos', [
            'title' => 'Meus Amigos - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'friends' => $friends,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function followers(): void
    {
        $userProfile = $this->api->getProfileDTO();
        $followersData = $this->api->getFollowers();
        $allFollowers = $followersData['people'] ?? [];

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalPages = (int)ceil(count($allFollowers) / $perPage);
        $followers = array_slice($allFollowers, ($page - 1) * $perPage, $perPage);

        $this->render('seguidores', [
            'title' => 'Meus Seguidores - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'followers' => $followers,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function activityFeed(): void
    {
        $userProfile = $this->api->getProfileDTO();
        $feedData = $this->api->getActivityFeed();
        $allActivities = $feedData['activityItems'] ?? [];

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalPages = (int)ceil(count($allActivities) / $perPage);
        $activities = array_slice($allActivities, ($page - 1) * $perPage, $perPage);

        $this->render('feed', [
            'title' => 'Feed Social - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'activities' => $activities,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function recentPlayers(): void
    {
        $userProfile = $this->api->getProfileDTO();
        $recentData = $this->api->getRecentPlayers();
        $allPlayers = $recentData['people'] ?? [];

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalPages = (int)ceil(count($allPlayers) / $perPage);
        $players = array_slice($allPlayers, ($page - 1) * $perPage, $perPage);

        $this->render('recentes', [
            'title' => 'Jogadores Recentes - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'players' => $players,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }

    public function blocks(): void
    {
        $userProfile = $this->api->getProfileDTO();
        $blocksData = $this->api->get("friends/mute"); 
        $allBlocks = $blocksData['people'] ?? [];

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 12;
        $totalPages = (int)ceil(count($allBlocks) / $perPage);
        $blocks = array_slice($allBlocks, ($page - 1) * $perPage, $perPage);

        $this->render('bloqueados', [
            'title' => 'Bloqueados - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'blocks' => $blocks,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ], 'main');
    }
}
