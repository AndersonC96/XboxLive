<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();
use Anderson\XboxLive\Services\OpenXBLService;

$api = new OpenXBLService();
$account = $api->getAccount();
$xuid = $account['profileUsers'][0]['id'] ?? ($account['profileUsers'][0]['hostId'] ?? null);

$titleId = '591405932'; // Red Dead Redemption 2 (Modern)

$titleId = '591405932'; // Red Dead Redemption 2 (Modern)

if ($xuid) {
    echo "Fetching achievements for Title ID $titleId (Modern) WITH XUID...\n";
    $achievements = $api->get("achievements/player/$xuid/title/$titleId");
    if ($achievements) {
        echo "Root Keys: " . implode(', ', array_keys($achievements)) . "\n";
        if (isset($achievements['achievements'])) {
            echo "Found achievements key. Count: " . count($achievements['achievements']) . "\n";
            if (count($achievements['achievements']) == 0) {
                echo "Full response:\n";
                print_r($achievements);
            }
        } else {
            echo "Achievements key MISSING.\n";
            print_r($achievements);
        }
    } else {
        echo "API failed.\n";
    }
}
