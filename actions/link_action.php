<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: ../pages/login.php');
    exit();
}

$user = AuthService::user();
$api = new OpenXBLService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $xuid = null;

    if ($action === 'auto') {
        // Obter XUID da conta dona da chave
        $account = $api->get("account");
        $xuid = $account['profileUsers'][0]['id'] ?? null;
    } elseif ($action === 'manual') {
        // Buscar XUID por gamertag
        $gamertag = trim($_POST['gamertag'] ?? '');
        if ($gamertag) {
            $search = $api->searchGamertag($gamertag);
            $xuid = $search['profileUsers'][0]['id'] ?? null;
        }
    }

    if ($xuid) {
        AuthService::updateXuid($user['id'], $xuid);
        header('Location: ../pages/perfil.php?status=success');
    } else {
        header('Location: ../pages/perfil.php?status=error');
    }
    exit();
}

header('Location: ../pages/perfil.php');
exit();
