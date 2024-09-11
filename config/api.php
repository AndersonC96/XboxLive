<?php
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $api_key = $_ENV['OPENXBL_API_KEY'];
    // Função para requisições GET
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
    // Função para requisições POST
    function openXBLPostRequest($endpoint, $body) {
        global $api_key;
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
        // Debugging opcional para verificar a resposta e o código HTTP
        /*echo "<pre>";
        echo "Corpo da requisição:\n" . json_encode($body) . "\n";
        echo "Código HTTP: " . $httpCode . "\n";
        if ($curlError) {
            echo "Erro cURL: " . $curlError . "\n";
        }
        echo "Resposta da API:\n" . $response . "\n";
        echo "</pre>";*/
        return json_decode($response, true);
    }