<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();
use Anderson\XboxLive\Services\OpenXBLService;

$api = new OpenXBLService();
$account = $api->get("account");

if ($account && isset($account['content'])) {
    echo "Found 'content' key. Unwrapping...\n";
    $profile = json_decode($account['content'], true);
    if ($profile && isset($profile['profileUsers'])) {
        echo "Successfully found profileUsers inside content JSON.\n";
        echo "XUID: " . $profile['profileUsers'][0]['id'] . "\n";
    } else {
        echo "Could not find profileUsers in decoded content.\n";
        print_r($profile);
    }
} else {
    echo "No 'content' key found. Full response keys: " . implode(', ', array_keys($account)) . "\n";
    print_r($account);
}
