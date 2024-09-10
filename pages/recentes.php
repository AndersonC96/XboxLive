<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require '../config/db.php';
    require_once '../config/api.php';
    if (!isset($_SESSION['user_id'])) {
        echo "Erro: Usuário não está logado.";
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT xuid FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['xuid']) {
        $xuid = $user['xuid'];
        $endpoint = "recent-players";
        $response = openXBLRequest($endpoint);
        if (isset($response['people']) && is_array($response['people'])) {
            $recentPlayers = $response['people'];
        } else {
            $recentPlayers = [];
        }
    } else {
        echo "Usuário não encontrado ou XUID não disponível.";
        exit;
    }
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Jogadores Recentes</h1>
    <?php if (!empty($recentPlayers)) : ?>
        <ul>
            <?php foreach ($recentPlayers as $player) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <?php
                            $avatar = !empty($player['displayPicRaw']) ? $player['displayPicRaw'] : '../img/default_avatar.jpg';
                            $lastPlayed = isset($player['recentPlayer']['titles'][0]['lastPlayedWithDateTime']) ? date('d/m/Y', strtotime($player['recentPlayer']['titles'][0]['lastPlayedWithDateTime'])) : 'Data não disponível';
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo htmlspecialchars($player['gamertag']); ?>" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($player['gamertag']); ?></p>
                            <p class="text-sm text-gray-400">Último jogo: <?php echo htmlspecialchars($player['recentPlayer']['titles'][0]['titleName']); ?></p>
                            <p class="text-sm text-gray-400">Último encontro: <?php echo $lastPlayed; ?></p>
                            <p class="text-sm text-gray-400">Gamerscore: <?php echo number_format($player['gamerScore'], 0, ',', '.'); ?> <img src="../img/gs.png" alt="Gamerscore Icon" class="inline-block w-4 h-4"></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Você não jogou com outros jogadores recentemente.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>