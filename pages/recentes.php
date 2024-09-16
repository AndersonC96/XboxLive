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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($recentPlayers as $player) : ?>
                <div class="bg-gray-800 text-white shadow-lg rounded-lg overflow-hidden flex">
                    <div class="w-1/3 bg-gray-700 flex items-center justify-center">
                        <?php
                            $avatar = !empty($player['displayPicRaw']) ? $player['displayPicRaw'] : '../img/default_avatar.jpg';
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo htmlspecialchars($player['gamertag']); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="w-2/3 p-4 flex flex-col justify-between">
                        <div>
                            <h2 class="text-lg font-bold"><?php echo htmlspecialchars($player['gamertag']); ?></h2>
                            <p class="text-sm text-gray-400 mt-1">Último jogo: <?php echo htmlspecialchars($player['recentPlayer']['titles'][0]['titleName']); ?></p>
                            <p class="text-sm text-gray-400">Último encontro:
                                <?php
                                    $lastPlayed = isset($player['recentPlayer']['titles'][0]['lastPlayedWithDateTime']) ? date('d/m/Y', strtotime($player['recentPlayer']['titles'][0]['lastPlayedWithDateTime'])) : 'Data não disponível';
                                    echo $lastPlayed;
                                ?>
                            </p>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm text-green-400 flex items-center">
                                Gamerscore: <?php echo number_format($player['gamerScore'], 0, ',', '.'); ?>
                                <img src="../img/gs.png" alt="Gamerscore Icon" class="inline-block ml-2 w-5 h-5">
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Você não jogou com outros jogadores recentemente.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>