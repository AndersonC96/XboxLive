<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

class StoreController extends BaseController
{
    private OpenXBLService $api;

    public function __construct()
    {
        $this->api = new OpenXBLService();
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
        $data = $this->api->getMostPlayed();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('mais_jogados', [
            'title' => 'Mais Jogados - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }

    public function deals(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getDeals();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('promocao', [
            'title' => 'Promoções - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }

    public function newGames(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getMarketplaceNew();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('novos_jogos', [
            'title' => 'Novos Jogos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }

    public function topPaid(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getTopPaid();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('mais_jogados', [
            'title' => 'Top Pagos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }

    public function topFree(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getTopFree();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('mais_jogados', [
            'title' => 'Top Gratuitos - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }

    public function bestRated(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getBestRated();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('mais_jogados', [
            'title' => 'Melhores Avaliados - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }

    public function comingSoon(): void
    {
        $profile = $this->api->getAccount();
        $userProfile = $this->getUserProfileStats($profile['profileUsers'][0] ?? null);
        $data = $this->api->getMarketplaceComingSoon();
        $games = $data['Products'] ?? ($data['products'] ?? ($data ?: []));

        $this->render('novos_jogos', [
            'title' => 'Chegando em Breve - Xbox Store',
            'showNavbar' => true,
            'userProfile' => $userProfile,
            'games' => $games
        ], 'main');
    }
}
