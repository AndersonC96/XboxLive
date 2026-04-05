<?php

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Config;

class OpenXBLService
{
    private $apiKey;
    private $baseUrl = "https://xbl.io/api/v2/";
    private $language = "pt-BR";

    public function __construct()
    {
        $this->apiKey = Config::get('OPENXBL_API_KEY') ?: Config::get('XBOX_API_KEY');
    }

    // --- Core API Helpers ---

    public function get($endpoint)
    {
        return $this->request($endpoint);
    }

    public function post($endpoint, $body)
    {
        return $this->request($endpoint, 'POST', $body);
    }

    // --- Account & Profile ---

    public function getProfile($xuid = null)
    {
        return $xuid ? $this->get("account/$xuid") : $this->get("account");
    }

    public function getAlerts()
    {
        return $this->get("alerts");
    }

    public function searchGamertag($gamertag)
    {
        return $this->get("friends/search?gt=" . urlencode($gamertag));
    }

    // --- Social & Friends ---

    public function getFriends($xuid = null)
    {
        return $xuid ? $this->get("friends/$xuid") : $this->get("friends");
    }

    public function getPresence($xuid = null)
    {
        return $xuid ? $this->get("presence/$xuid") : $this->get("presence");
    }

    // --- Marketplace & Games ---

    public function getMarketplaceDetails($productIds)
    {
        // Support array of IDs or single ID
        $ids = is_array($productIds) ? implode(',', $productIds) : $productIds;
        return $this->post("marketplace/details", ['products' => $ids]);
    }

    public function getTitleHistory($xuid = null)
    {
        return $xuid ? $this->get("titles/$xuid") : $this->get("titles");
    }

    public function getAchievements($xuid = null)
    {
        return $xuid ? $this->get("achievements/player/$xuid") : $this->get("achievements");
    }

    // --- DVR (Media) ---

    public function getScreenshots($xuid = null)
    {
        return $xuid ? $this->get("dvr/screenshots/$xuid") : $this->get("dvr/screenshots");
    }

    public function getGameClips($xuid = null)
    {
        return $xuid ? $this->get("dvr/gameclips/$xuid") : $this->get("dvr/gameclips");
    }

    // --- Activity ---

    public function getActivityFeed()
    {
        return $this->get("activity/feed");
    }

    // --- Private Request Logic ---

    private function request($endpoint, $method = 'GET', $body = null)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $url = $this->baseUrl . ltrim($endpoint, '/');
        $headers = [
            "X-Authorization: " . $this->apiKey,
            "Content-Type: application/json",
            "Accept-Language: " . $this->language
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        // Auto-unwrap 'content' if present
        if (isset($decoded['content'])) {
            $content = $decoded['content'];
            if (is_string($content)) {
                $unwrapped = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $unwrapped;
                }
            }
            return $content;
        }

        return $decoded;
    }
}
