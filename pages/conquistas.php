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
                return '<img src="../img/xboxseries.png" alt="Xbox Series" width="62,5" height="62,5">';
            case 'PC':
                return '<img src="../img/windows.png" alt="PC" width="62,5" height="62,5">';
            case 'XboxOne':
                return '<img src="../img/xboxone.png" alt="Xbox One" width="62,5" height="62,5">';
            case 'Win32':
                return '<img src="../img/windows.png" alt="PC" width="62,5" height="62,5">';
            case 'Mobile':
                return '<img src="../img/windowsphone.webp" alt="Windows Phone" width="62,5" height="62,5">';
            case 'Xbox360':
                return '<img src="../img/xbox360.png" alt="Xbox 360" width="62,5" height="62,5">';
            default:
                return $deviceType;
        }
    }
    function getBoxArt($game) {
        $defaultImage = '../img/default_game.jpg';
        if (isset($game['images'])) {
            foreach ($game['images'] as $image) {
                if ($image['type'] === 'BoxArt') {
                    return $image['url'];
                }
            }
        }
        return $defaultImage;
    }
    $items_per_page = 12;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $total_games = count($games);
    $games_to_display = array_slice($games, $offset, $items_per_page);
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Conquistas</h1>
    <?php if (!empty($games_to_display)) : ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($games_to_display as $game) : ?>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md flex">
                    <div class="relative w-1/3">
                        <img src="<?php echo getBoxArt($game); ?>" alt="Imagem do jogo" class="object-cover rounded-l-md h-full">
                    </div>
                    <div class="w-2/3 p-4">
                        <p class="text-xl font-bold text-white"><?php echo htmlspecialchars($game['name']); ?></p>
                        <p class="text-sm text-gray-400">Gamerscore: <?php echo number_format($game['achievement']['currentGamerscore'], 0, ',', '.'); ?> <img src="../img/gs.png" alt="Gamerscore Icon" class="inline-block w-4 h-4"></p>
                        <p class="text-sm text-gray-400">Progresso: <?php echo $game['achievement']['progressPercentage']; ?>%</p>
                        <p class="text-sm text-gray-400 flex items-center space-x-2">Plataformas:
                            <?php
                                if (isset($game['devices']) && is_array($game['devices'])) {
                                    foreach ($game['devices'] as $device) {
                                        echo getDeviceIcon($device);
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
            <?php endforeach; ?>
        </div>
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