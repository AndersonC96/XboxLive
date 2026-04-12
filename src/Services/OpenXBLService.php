<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Config;
use Anderson\XboxLive\Core\Cache;
use Anderson\XboxLive\Exceptions\XblApiException;
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
        $this->apiKey = (string)(Config::get('OPENXBL_API_KEY') ?: Config::get('XBOX_API_KEY') ?: '');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'X-Authorization' => $this->apiKey,
                'Content-Type'    => 'application/json',
                'Accept-Language' => $this->language
            ],
            'http_errors' => false,
            'timeout' => 10.0
        ]);
    }

    private function request(string $method, string $endpoint, array $options = []): mixed
    {
        if (empty($this->apiKey)) {
            throw new XblApiException("API Key não configurada no arquivo .env");
        }

        try {
            $response = $this->client->request($method, ltrim($endpoint, '/'), $options);
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 429) {
                throw new XblApiException("Limite de requisições à API atingido (Rate Limit).");
            }

            if ($statusCode >= 400) {
                return null;
            }

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $decoded['content'] ?? $decoded;
        } catch (GuzzleException $e) {
            throw new XblApiException("Erro de conexão com a Xbox Live: " . $e->getMessage());
        }
    }

    public function get(string $endpoint, bool $useCache = false): mixed
    {
        if ($useCache) {
            $cached = Cache::get($endpoint);
            if ($cached) return $cached;
        }

        $response = $this->request('GET', $endpoint);
        
        if ($useCache && $response) {
            Cache::set($endpoint, $response, 3600); // 1h cache por padrão para GETs comuns
        }

        return $response;
    }

    public function post(string $endpoint, array $body): mixed
    {
        return $this->request('POST', $endpoint, ['json' => $body]);
    }

    // --- Mapeamento de Endpoints ---

    public function getAccount(): ?array { return $this->get("account", true); }
    
    public function getProfile(?string $xuid = null): ?array { 
        return $xuid ? $this->get("account/$xuid", true) : $this->getAccount(); 
    }

    public function getFriends(?string $xuid = null): ?array { 
        return $xuid ? $this->get("friends/$xuid", true) : $this->get("friends", true); 
    }

    public function getFollowers(): ?array { return $this->get("followers", true); }

    public function getPresence(?string $xuid = null): ?array { 
        return $xuid ? $this->get("presence/$xuid") : $this->get("presence"); 
    }

    public function getAchievementsHistory(?string $xuid = null): ?array {
        $endpoint = $xuid ? "achievements/player/$xuid/history" : "achievements/history";
        return $this->get($endpoint, true);
    }

    public function getTitleHistory(?string $xuid = null): ?array {
        $endpoint = $xuid ? "player/titleHistory/$xuid" : "player/titleHistory";
        return $this->get($endpoint, true);
    }

    public function getProductDetails(array|string $productIds): ?array {
        $ids = is_array($productIds) ? $productIds : explode(',', $productIds);
        $cacheKey = 'products_v2_' . implode('_', $ids);
        
        $cached = Cache::get($cacheKey);
        if ($cached) return $cached;

        $response = $this->post("marketplace/details", ['products' => implode(',', $ids)]);
        if ($response) Cache::set($cacheKey, $response, 86400); // 24h
        return $response;
    }

    public function getGamePassAll(): ?array { return $this->get("gamepass/all", true); }
    public function getEAPlayAll(): ?array { return $this->get("gamepass/ea-play", true); }
    public function getPCGamePassAll(): ?array { return $this->get("gamepass/pc", true); }
    public function getNoControllerGames(): ?array { return $this->get("gamepass/no-controller", true); }
    public function getNewGamePass(): ?array { return $this->get("gamepass/new", true); }
    public function getComingSoon(): ?array { return $this->get("gamepass/coming", true); }
    public function getLeavingSoon(): ?array { return $this->get("gamepass/leaving", true); }

    public function getMostPlayed(): ?array { return $this->get("marketplace/most-played", true); }
    public function getDeals(): ?array { return $this->get("marketplace/deals", true); }
    public function getMarketplaceNew(): ?array { return $this->get("marketplace/new", true); }
    public function getTopPaid(): ?array { return $this->get("marketplace/top-paid", true); }
    public function getTopFree(): ?array { return $this->get("marketplace/top-free", true); }
    public function getBestRated(): ?array { return $this->get("marketplace/best-rated", true); }
    public function getMarketplaceComingSoon(): ?array { return $this->get("marketplace/coming-soon", true); }
    public function getStoreHome(): ?array { return $this->get("marketplace/store-home", true); }

    public function getActivityFeed(): ?array { return $this->get("activity/feed"); }
    public function getRecentPlayers(): ?array { return $this->get("activity/recent-players"); }
    public function searchGamertag(string $gt): ?array { return $this->get("friends/search?gt=" . urlencode($gt)); }
    
    public function getScreenshots(?string $xuid = null): ?array {
        return $xuid ? $this->get("dvr/screenshots/$xuid", true) : $this->get("dvr/screenshots", true);
    }

    public function getGameClips(?string $xuid = null): ?array {
        return $xuid ? $this->get("dvr/gameclips/$xuid", true) : $this->get("dvr/gameclips", true);
    }

    public function getAchievementsForTitle(string $titleId, ?string $xuid = null): ?array {
        $endpoint = $xuid ? "achievements/player/$xuid/title/$titleId" : "achievements/title/$titleId";
        return $this->get($endpoint, true);
    }
}
