<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    require '../config/db.php';
    
    // Obter informações da conta (inclui XUID nos metadados)
    $accountResponse = openXBLRequest("account");
    $xuid = null;
    
    if ($accountResponse && isset($accountResponse['profileUsers'][0]['id'])) {
        $xuid = $accountResponse['profileUsers'][0]['id'];
    }
    
    $presenceData = null;
    $lastSeenDetails = null;
    
    if ($xuid) {
        $endpoint = $xuid . "/presence";
        $response = openXBLRequest($endpoint);
        if ($response && isset($response[0]['state'])) {
            $presenceData = $response[0]['state'];
            if ($response[0]['state'] === 'Offline' && isset($response[0]['lastSeen'])) {
                $lastSeenDetails = $response[0]['lastSeen'];
            }
        } else {
            $presenceData = 'Desconhecido';
        }
    } else {
        $presenceData = 'Não disponível';
    }
    function getDeviceIcon($deviceType) {
        switch ($deviceType) {
            case 'Scarlett':
                return '<img src="../img/xboxseries.png" alt="Xbox Series" class="inline-block w-6 h-6">';
            case 'Android':
                return '<img src="../img/android.svg" alt="Android" class="inline-block w-6 h-6">';
            case 'WindowsOneCore':
                return '<img src="../img/windows.png" alt="Windows" class="inline-block w-6 h-6">';
            case 'XboxOne':
                return '<img src="../img/xboxone.png" alt="Xbox One" class="inline-block w-6 h-6">';
            case 'iOS':
                return '<img src="../img/apple.png" alt="iOS" class="inline-block w-6 h-6">';
            case 'PlayStation':
                return '<img src="../img/playstation.png" alt="PlayStation" class="inline-block w-6 h-6">';
            case 'Web':
                return '<img src="../img/xcloud.png" alt="Web" class="inline-block w-6 h-6">';
            default:
                return $deviceType;
        }
    }
?>
<div class="container mx-auto mt-10">
    <div class="bg-gray-800 p-4 rounded-lg shadow-md">
        <p><strong>Status</strong>: <?php echo $presenceData; ?></p>
        <?php if ($lastSeenDetails): ?>
            <div class="mt-4">
                <p><strong>Visto por último</strong></p>
                <p><b>Dispositivo</b>: <?php echo getDeviceIcon($lastSeenDetails['deviceType']); ?></p>
                <p><b>Jogo / Aplicativo</b>: <?php echo $lastSeenDetails['titleName']; ?></p>
                <p><b>Última vez online</b>: <?php echo date('d/m/Y H:i:s', strtotime($lastSeenDetails['timestamp'])); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include('../includes/footer.php'); ?>