<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "clubs/recommendations";
    $response = openXBLRequest($endpoint);
    $clubs = $response['clubs'] ?? [];
?>
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Recomendações de Clubes</h1>
    <?php if (!empty($clubs)): ?>
        <ul class="bg-gray-800 p-4 rounded-lg shadow-md">
            <?php foreach ($clubs as $club): ?>
                <li class="border-b border-gray-700 py-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo $club['profile']['displayImageUrl']['value'] ?? '../img/default_club.png'; ?>" alt="Imagem do Clube" class="w-16 h-16 rounded-full">
                        <div>
                            <h2 class="text-xl font-bold text-white"><?php echo $club['profile']['name']['value']; ?></h2>
                            <p class="text-gray-400"><?php echo $club['profile']['description']['value'] ?? 'Sem descrição'; ?></p>
                            <p class="text-gray-400">Membros: <?php echo $club['membersCount']; ?></p>
                            <p class="text-gray-400">Seguidores: <?php echo $club['followersCount']; ?></p>
                            <p class="text-gray-400">Tags: <?php echo implode(', ', $club['profile']['tags']['value'] ?? []); ?></p>
                            <p class="text-gray-400">Recomendado por: <?php echo implode(', ', array_column($club['recommendation']['reasons'], 'localizedText')); ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-gray-400">Regras: <?php echo $club['profile']['rules']['value'] ?? 'Sem regras definidas'; ?></p>
                        <p class="text-gray-400">Localização preferida: <?php echo $club['profile']['preferredLocale']['value']; ?></p>
                        <p class="text-gray-400">Membros no clube hoje: <?php echo $club['clubPresenceTodayCount']; ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-white">Nenhuma recomendação de clube disponível no momento.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>