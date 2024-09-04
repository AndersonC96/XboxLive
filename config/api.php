<?php
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $api_key = $_ENV['OPENXBL_API_KEY'];
    function openXBLRequest($endpoint) {
        global $api_key;
        $url = "https://xbl.io/api/v2/" . $endpoint;
        $headers = [
            "X-Authorization: $api_key"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }