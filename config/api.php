<?php

require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\OpenXBLService;

// Backward compatibility: expose global functions
$global_api_service = new OpenXBLService();

function openXBLRequest($endpoint) {
    global $global_api_service;
    return $global_api_service->get($endpoint);
}

function openXBLPostRequest($endpoint, $body) {
    global $global_api_service;
    return $global_api_service->post($endpoint, $body);
}