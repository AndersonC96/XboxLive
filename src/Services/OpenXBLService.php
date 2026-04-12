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

    public function getAchievementsForTitle(string $titleId, ?string $xuid = null)
    {
        // Primeiro tentamos com o XUID (Geralmente necessário para Xbox 360)
        $response = $xuid ? $this->get("achievements/player/$xuid/title/$titleId") : $this->get("achievements/title/$titleId");
        
        // Se retornar vazio, tentamos sem o XUID (Muitos títulos modernos no v2 só retornam assim com o contexto do usuário autenticado)
        if ($xuid && (empty($response['achievements']) || count($response['achievements']) === 0)) {
            $fallback = $this->get("achievements/title/$titleId");
            if (!empty($fallback['achievements'])) {
                return $fallback;
            }
        }

        return $response;
    }

    public function getAchievementsHistory(?string $xuid = null)
    {
        // Retorna o histórico de títulos com resumo de conquistas (V3)
        return $xuid ? $this->get("achievements/player/$xuid/history") : $this->get("achievements/history");
    }

    public function getAchievementsV3(?string $xuid)
    {
        return $this->getAchievementsHistory($xuid);
    }

    public function getTitleHistory(?string $xuid = null)
    {
        return $xuid ? $this->get("player/titleHistory/$xuid") : $this->get("player/titleHistory");
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

    public function getEAPlayAll()
    {
        return $this->get("gamepass/ea-play");
    }

    public function getPCGamePassAll()
    {
        return $this->get("gamepass/pc");
    }

    public function getNoControllerGames()
    {
        return $this->get("gamepass/no-controller");
    }

    public function getNewGamePass()
    {
        return $this->get("gamepass/new");
    }

    public function getComingSoon()
    {
        return $this->get("gamepass/coming");
    }

    public function getLeavingSoon()
    {
        return $this->get("gamepass/leaving");
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
