<?php

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenXBLService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = "https://xbl.io/api/v2/";
    private string $language = "pt-BR";

    public function __construct()
    {
        $this->apiKey = Config::get('OPENXBL_API_KEY') ?: Config::get('XBOX_API_KEY') ?: '';
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'X-Authorization' => $this->apiKey,
                'Content-Type'    => 'application/json',
                'Accept-Language' => $this->language
            ],
            'http_errors' => false // Para tratarmos os erros manualmente
        ]);
    }

    private function request(string $method, string $endpoint, array $options = [])
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = $this->client->request($method, ltrim($endpoint, '/'), $options);
            $statusCode = $response->getStatusCode();
            
            if ($statusCode >= 400) {
                return null;
            }

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            // Auto-unwrap 'content' if it's a JSON string
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
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function get(string $endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    public function post(string $endpoint, array $body)
    {
        return $this->request('POST', $endpoint, ['json' => $body]);
    }

    // --- Account & Profile (API v2) ---

    public function getAccount()
    {
        return $this->get("account");
    }

    public function getProfile(string $xuid = null)
    {
        return $xuid ? $this->get("account/$xuid") : $this->getAccount();
    }

    public function getAlerts()
    {
        return $this->get("alerts");
    }

    public function searchGamertag(string $gamertag)
    {
        return $this->get("friends/search?gt=" . urlencode($gamertag));
    }

    public function getPlayerSummary($xuids = null)
    {
        return $xuids ? $this->get("player/summary/$xuids") : $this->get("player/summary");
    }

    // --- Social & Friends ---

    public function getFriends(string $xuid = null)
    {
        return $xuid ? $this->get("friends/$xuid") : $this->get("friends");
    }

    public function getFollowers()
    {
        return $this->get("followers");
    }

    public function getPresence(string $xuid = null)
    {
        return $xuid ? $this->get("presence/$xuid") : $this->get("presence");
    }

    // --- Achievements ---

    public function getAchievements(string $xuid = null)
    {
        return $xuid ? $this->get("achievements/player/$xuid") : $this->get("achievements");
    }

    public function getAchievementsForTitle(string $titleId, string $xuid = null)
    {
        $endpoint = $xuid ? "achievements/player/$xuid/title/$titleId" : "achievements/title/$titleId";
        return $this->get($endpoint);
    }

    public function getAchievementsV3(string $xuid)
    {
        return $this->get("achievements/player/$xuid/history");
    }

    // --- DVR (Media) ---

    public function getScreenshots(string $xuid = null)
    {
        return $xuid ? $this->get("dvr/screenshots/$xuid") : $this->get("dvr/screenshots");
    }

    public function getGameClips(string $xuid = null)
    {
        return $xuid ? $this->get("dvr/gameclips/$xuid") : $this->get("dvr/gameclips");
    }

    // --- Marketplace ---

    public function getGamePass()
    {
        return $this->get("marketplace/gamepass");
    }

    public function getGamePassAll()
    {
        return $this->get("gamepass/all");
    }

    public function searchMarketplace(string $query)
    {
        return $this->get("marketplace/search?q=" . urlencode($query));
    }

    public function getProductDetails($productIds)
    {
        $ids = is_array($productIds) ? $productIds : explode(',', $productIds);
        return $this->post("marketplace/details", ['products' => implode(',', $ids)]);
    }

    // --- Activity Feed ---

    public function getActivityFeed()
    {
        return $this->get("activity/feed");
    }

    public function getRecentPlayers()
    {
        return $this->get("activity/recent-players");
    }

    public function getActivityHistory(string $xuid = null)
    {
        return $xuid ? $this->get("activity/history/$xuid") : $this->get("activity/history");
    }
}
