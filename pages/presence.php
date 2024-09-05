<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    require '../config/db.php';
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT xuid FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $presenceData = null;
    $lastSeenDetails = null;
    if ($user && $user['xuid']) {
        $xuid = $user['xuid'];
        $endpoint = $xuid . "/presence";
        $response = openXBLRequest($endpoint);
        if (isset($response[0]['state'])) {
            $presenceData = $response[0]['state'];
            if ($response[0]['state'] === 'Offline' && isset($response[0]['lastSeen'])) {
                $lastSeenDetails = $response[0]['lastSeen'];
            }
        } else {
            $presenceData = 'Desconhecido';
        }
    }
?>
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Presença</h1>
    <div class="bg-gray-800 p-4 rounded-lg shadow-md">
        <p><strong>Status</strong>: <?php echo $presenceData; ?></p>
        <?php if ($lastSeenDetails): ?>
            <div class="mt-4">
                <p><strong>Último Acesso</strong></p>
                <p><b>Dispositivo</b>: <?php echo $lastSeenDetails['deviceType']; ?></p>
                <p><b>Jogo / Aplicativo</b>: <?php echo $lastSeenDetails['titleName']; ?></p>
                <p><b>Última vez online</b>: <?php echo date('d/m/Y H:i:s', strtotime($lastSeenDetails['timestamp'])); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include('../includes/footer.php'); ?>