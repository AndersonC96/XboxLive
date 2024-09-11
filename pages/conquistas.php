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
        $endpoint = "achievements";
        $response = openXBLRequest($endpoint);
        if (isset($response['titles']) && is_array($response['titles'])) {
            $games = $response['titles'];
        } else {
            $games = [];
        }
    } else {
        echo "Usuário não encontrado ou XUID não disponível.";
        exit;
    }
    function getDeviceIcon($deviceType) {
        switch ($deviceType) {
            case 'XboxSeries':
                return '<img src="../img/xboxseries.png" alt="Xbox Series" class="inline-block w-6 h-6">';
            case 'PC':
                return '<img src="../img/windows.png" alt="PC" class="inline-block w-6 h-6">';
            case 'XboxOne':
                return '<img src="../img/xboxone.png" alt="Xbox One" class="inline-block w-6 h-6">';
            case 'Win32':
                return '<img src="../img/windows.png" alt="PC" class="inline-block w-6 h-6">';
            case 'Mobile':
                return '<img src="../img/windowsphone.webp" alt="Windows Phone" class="inline-block w-6 h-6">';
            case 'Xbox360':
                return '<img src="../img/xbox360.png" alt="Xbox 360" class="inline-block w-6 h-6">';
            default:
                return $deviceType;
    }
}
    $items_per_page = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $total_games = count($games);
    $games_to_display = array_slice($games, $offset, $items_per_page);
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Conquistas</h1>
    <?php if (!empty($games_to_display)) : ?>
        <ul>
            <?php foreach ($games_to_display as $game) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo $game['displayImage']; ?>" alt="Imagem do jogo" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($game['name']); ?></p>
                            <p class="text-sm text-gray-400">Gamerscore: <?php echo number_format($game['achievement']['currentGamerscore'], 0, ',', '.'); ?> <img src="../img/gs.png" alt="Gamerscore Icon" class="inline-block w-4 h-4"></p>
                            <p class="text-sm text-gray-400">Progresso: <?php echo $game['achievement']['progressPercentage']; ?>%</p>
                            <p class="text-sm text-gray-400">Plataformas:
                                <?php
                                if (isset($game['devices']) && is_array($game['devices'])) {
                                    foreach ($game['devices'] as $device) {
                                        echo getDeviceIcon($device) . ' ';
                                    }
                                } else {
                                    echo 'Plataforma não disponível';
                                }
                                ?>
                            </p>
                            <p class="text-sm text-gray-400">Jogado pela última vez:
                                <?php
                                $lastPlayed = new DateTime($game['titleHistory']['lastTimePlayed']);
                                echo $lastPlayed->format('d/m/Y');
                                ?>
                            </p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-4">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="bg-gray-800 text-white px-4 py-2 rounded">Anterior</a>
            <?php endif; ?>
            <?php if ($page * $items_per_page < $total_games): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="bg-gray-800 text-white px-4 py-2 rounded">Próxima</a>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <p>Você não possui conquistas.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>