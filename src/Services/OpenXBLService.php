<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Config;
use Anderson\XboxLive\Core\Cache;
use Anderson\XboxLive\Exceptions\XblApiException;
use Anderson\XboxLive\DTO\GamerProfile;
use Anderson\XboxLive\DTO\GameProduct;
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
            'timeout' => 15.0
        ]);
    }

    private function request(string $method, string $endpoint, array $options = []): mixed
    {
        if (empty($this->apiKey)) {
            throw new XblApiException("Xbox API Key is missing in .env configuration.");
        }

        try {
            $response = $this->client->request($method, ltrim($endpoint, '/'), $options);
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 401) {
                throw new XblApiException("Invalid Xbox API Key. Please check your credentials.");
            }

            if ($statusCode === 429) {
                throw new XblApiException("Rate Limit reached. Please wait a moment before trying again.");
            }

            if ($statusCode >= 500) {
                throw new XblApiException("Xbox Live services are currently unavailable (500).");
            }

            if ($statusCode >= 400) {
                return null;
            }

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);

            return $decoded['content'] ?? $decoded;
        } catch (GuzzleException $e) {
            error_log("Guzzle Error: " . $e->getMessage());
            throw new XblApiException("Connection failure with Xbox Live. Please check your internet.");
        }
    }

    public function getProfileDTO(?string $xuid = null): GamerProfile
    {
        $data = $xuid ? $this->request('GET', "account/$xuid") : $this->request('GET', "account");
        $sessionUser = AuthService::user();
        
        $profileData = $data['profileUsers'][0] ?? [];
        return GamerProfile::fromXblArray($profileData, $sessionUser);
    }

    public function getProductsDTO(array|string $ids): array
    {
        $idList = is_array($ids) ? $ids : explode(',', $ids);
        if (empty($idList)) return [];

        $cacheKey = 'products_dto_' . implode('_', $idList);
        $cached = Cache::get($cacheKey);
        if ($cached) return $cached;

        $response = $this->request('POST', "marketplace/details", ['json' => ['products' => implode(',', $idList)]]);
        $rawProducts = $response['Products'] ?? [];
        
        $products = array_map(fn($p) => GameProduct::fromXblArray($p), $rawProducts);
        
        Cache::set($cacheKey, $products, 86400);
        return $products;
    }

    public function getProductDTO(string $id): ?GameProduct
    {
        $products = $this->getProductsDTO([$id]);
        return $products[0] ?? null;
    }

    // --- Endpoints de Sync (Raw IDs) ---
    public function getGamePassAll(): array { return $this->request('GET', "gamepass/all") ?? []; }
    public function getEAPlayAll(): array { return $this->request('GET', "gamepass/ea-play") ?? []; }
    public function getPCGamePassAll(): array { return $this->request('GET', "gamepass/pc") ?? []; }
    public function getNoControllerGames(): array { return $this->request('GET', "gamepass/no-controller") ?? []; }
    public function getNewGamePass(): array { return $this->request('GET', "gamepass/new") ?? []; }
    public function getComingSoon(): array { return $this->request('GET', "gamepass/coming") ?? []; }
    public function getLeavingSoon(): array { return $this->request('GET', "gamepass/leaving") ?? []; }

    // --- Social & History (Raw Data for complex processing) ---
    public function getFriends(): array { return $this->request('GET', "friends") ?? []; }
    public function getFollowers(): array { return $this->request('GET', "followers") ?? []; }
    public function getActivityFeed(): array { return $this->request('GET', "activity/feed") ?? []; }
    public function getTitleHistory(?string $xuid = null): array {
        $endpoint = $xuid ? "player/titleHistory/$xuid" : "player/titleHistory";
        return $this->request('GET', $endpoint) ?? [];
    }
    public function getRecentPlayers(): array { return $this->request('GET', "activity/recent-players") ?? []; }
    public function getPresence(?string $xuid = null): array {
        $endpoint = $xuid ? "presence/$xuid" : "presence";
        return $this->request('GET', $endpoint) ?? [];
    }
}
