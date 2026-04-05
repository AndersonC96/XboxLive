<?php

namespace Anderson\XboxLive\Services;

use Anderson\XboxLive\Core\Config;

class OpenXBLService
{
    private $apiKey;
    private $baseUrl = "https://xbl.io/api/v2/";

    public function __construct()
    {
        $this->apiKey = Config::get('OPENXBL_API_KEY') ?: Config::get('XBOX_API_KEY');
    }

    public function get($endpoint)
    {
        return $this->request($endpoint);
    }

    public function post($endpoint, $body)
    {
        return $this->request($endpoint, 'POST', $body);
    }

    private function request($endpoint, $method = 'GET', $body = null)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $url = $this->baseUrl . ltrim($endpoint, '/');
        $headers = [
            "X-Authorization: " . $this->apiKey,
            "Content-Type: application/json"
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

        return $decoded;
    }
}
