<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "alerts";
    $response = openXBLRequest($endpoint);
    $alerts = $response['alerts'] ?? [];
?>
<div class="container mx-auto mt-10">
    <?php if (!empty($alerts)): ?>
        <ul class="bg-gray-800 p-4 rounded-lg shadow-md">
            <?php foreach ($alerts as $alert): ?>
                <li class="border-b border-gray-700 py-2">
                    <p><strong><?php echo $alert['action']; ?></strong></p>
                    <p><?php echo $alert['actorGamertag']; ?></p>
                    <p><?php echo isset($alert['message']) ? $alert['message'] : 'Sem mensagem'; ?></p>
                    <p><?php echo date('d/m/Y H:i:s', strtotime($alert['timestamp'])); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Não há alertas disponíveis no momento.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>