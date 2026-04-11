<?php

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

class DashboardController extends BaseController
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

        // Extract Stats for View
        $stats = [
            'gamertag'   => 'Usuário',
            'gamerpic'   => 'img/default_avatar.jpg',
            'gamerscore' => '0',
            'tier'       => 'Sliver',
            'reputation' => 'Good',
            'bio'        => '-',
            'location'   => '-'
        ];

        if ($profileData && isset($profileData['settings'])) {
            foreach ($profileData['settings'] as $setting) {
                switch ($setting['id']) {
                    case 'Gamertag': $stats['gamertag'] = $setting['value']; break;
                    case 'GameDisplayPicRaw': $stats['gamerpic'] = $setting['value']; break;
                    case 'Gamerscore': $stats['gamerscore'] = number_format($setting['value'], 0, ',', '.'); break;
                    case 'AccountTier': $stats['tier'] = $setting['value']; break;
                    case 'XboxOneRep': $stats['reputation'] = $setting['value']; break;
                    case 'Bio': $stats['bio'] = $setting['value']; break;
                    case 'Location': $stats['location'] = $setting['value']; break;
                }
            }
        }

        $this->render('dashboard', [
            'title' => 'Dashboard - Xbox Live',
            'showNavbar' => true,
            'userProfile' => $stats,
            'presenceState' => $presenceState,
            'presenceText' => $presenceText,
            'recentTitles' => $recentTitles
        ], 'main');
    }
}
