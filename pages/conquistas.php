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
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Conquistas</h1>
    <?php if (!empty($games)) : ?>
        <ul>
            <?php foreach ($games as $game) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo $game['displayImage']; ?>" alt="Imagem do jogo" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($game['name']); ?></p>
                            <p class="text-sm text-gray-400">Gamerscore: <?php echo number_format($game['achievement']['currentGamerscore'], 0, ',', '.'); ?> G</p>
                            <p class="text-sm text-gray-400">Progresso: <?php echo $game['achievement']['progressPercentage']; ?>%</p>
                            <p class="text-sm text-gray-400">Plataforma:
                                <?php echo implode(', ', $game['achievement']['devices']); ?>
                            </p>
                            <p class="text-sm text-gray-400">Jogado pela última vez:
                                <?php
                                $lastPlayed = new DateTime($game['achievement']['lastTimePlayed']);
                                echo $lastPlayed->format('d/m/Y');
                                ?>
                            </p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Você não possui conquistas.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>