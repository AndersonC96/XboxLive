<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "activity/history";
    $response = openXBLRequest($endpoint);
    $activityItems = $response['activityItems'] ?? [];
?>
<div class="container mx-auto mt-10">
    <?php if (!empty($activityItems)): ?>
        <ul class="bg-gray-800 p-4 rounded-lg shadow-md">
            <?php foreach ($activityItems as $item): ?>
                <li class="border-b border-gray-700 py-4">
                    <div class="flex items-center">
                        <img src="<?php echo $item['authorInfo']['imageUrl']; ?>" alt="Avatar" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="text-lg font-bold text-green-400"><?php echo $item['authorInfo']['modernGamertag']; ?></p>
                        </div>
                    </div>
                    <p class="mt-2 text-white"><strong><?php echo $item['description']; ?></strong></p>
                    <p class="text-gray-400"><?php echo $item['shortDescription']; ?></p>
                    <p class="text-gray-500 text-sm mt-1">Jogo: <?php echo $item['contentTitle']; ?></p>
                    <p class="text-gray-500 text-sm">Plataforma:
                        <?php if ($item['platform'] === 'Scarlett'): ?>
                            <img src="../img/xboxseries.png" alt="Xbox Series" class="inline w-6 h-6">
                        <?php else: ?>
                            <?php echo $item['platform']; ?>
                        <?php endif; ?>
                    </p>
                    <img src="<?php echo $item['screenshotUri']; ?>" alt="Screenshot" class="mt-2 rounded-lg shadow-lg w-full">
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-gray-500 text-xs">Capturado em: <?php echo date('d/m/Y H:i', strtotime($item['date'])); ?></p>
                    </div>
                    <div class="flex mt-2 space-x-4">
                        <p class="text-sm text-gray-400">Visualizações: <?php echo $item['viewCount']; ?></p>
                        <p class="text-sm text-gray-400">Likes: <?php echo $item['hasLiked'] ? '✔️' : '❌'; ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-gray-400">Nenhuma atividade disponível no histórico.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>