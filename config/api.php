<?php
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    // Try multiple sources/names for the API key and avoid undefined index warnings
    $api_key = getenv('OPENXBL_API_KEY') ?: getenv('XBOX_API_KEY') ?: (isset($_ENV['OPENXBL_API_KEY']) ? $_ENV['OPENXBL_API_KEY'] : (isset($_ENV['XBOX_API_KEY']) ? $_ENV['XBOX_API_KEY'] : null));
    // Função para requisições GET
    function openXBLRequest($endpoint) {
        global $api_key;
        if (empty($api_key)) {
            return null;
        }
        $url = "https://xbl.io/api/v2/" . $endpoint;
        $headers = [
            "X-Authorization: $api_key"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        if ($response === false) {
            return null;
        }
        $decoded = json_decode($response, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $decoded;
    }
    // Função para requisições POST
    function openXBLPostRequest($endpoint, $body) {
        global $api_key;
        if (empty($api_key)) {
            return null;
        }
        $url = "https://xbl.io/api/v2/" . $endpoint;
        $headers = [
            "X-Authorization: $api_key",
            "Content-Type: application/json"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Definir como requisição POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body)); // Enviar o corpo da requisição em JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Pegar o código de resposta HTTP
        $curlError = curl_error($ch); // Pegar erros do cURL, se houver
        curl_close($ch);
        if ($response === false) {
            return null;
        }
        $decoded = json_decode($response, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $decoded;
    }