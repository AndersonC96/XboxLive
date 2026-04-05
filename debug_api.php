<?php
require_once __DIR__ . '/vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();
use Anderson\XboxLive\Services\OpenXBLService;

$api = new OpenXBLService();
$account = $api->get("account");
if ($account) {
    echo "Account structure: " . implode(', ', array_keys($account)) . "\n";
    if (isset($account['profileUsers'])) {
        echo "Profile ID: " . ($account['profileUsers'][0]['id'] ?? 'N/A') . "\n";
    }
} else {
    echo "Account API failed.\n";
}

$search = $api->searchGamertag("AndersonC96");
if ($search) {
    echo "Search structure: " . implode(', ', array_keys($search)) . "\n";
    if (isset($search['profileUsers'])) {
        echo "Search Result ID: " . ($search['profileUsers'][0]['id'] ?? 'N/A') . "\n";
    }
} else {
    echo "Search API failed.\n";
}
