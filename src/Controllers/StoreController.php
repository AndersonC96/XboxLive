<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

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

    public function mostPlayed(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        // A API XBL.io pode retornar coleções de marketplace
        $games = $this->api->get("marketplace/most-played") ?? [];

        $this->render('mais_jogados', [
            'title' => 'Mais Jogados - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games['Products'] ?? []
        ], 'main');
    }

    public function deals(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $games = $this->api->get("marketplace/deals") ?? [];

        $this->render('promocao', [
            'title' => 'Promoções - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games['Products'] ?? []
        ], 'main');
    }

    public function newGames(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);

        $games = $this->api->get("marketplace/new-releases") ?? [];

        $this->render('novos_jogos', [
            'title' => 'Novos Jogos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games['Products'] ?? []
        ], 'main');
    }
}
