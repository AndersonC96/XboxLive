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
        $endpoint = "friends";
        $response = openXBLRequest($endpoint);
        if (isset($response['people']) && is_array($response['people'])) {
            $friends = $response['people'];
        } else {
            $friends = [];
        }
    } else {
        echo "Usuário não encontrado ou XUID não disponível.";
        exit;
    }
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Lista de Amigos</h1>
    <?php if (!empty($friends)) : ?>
        <ul>
            <?php foreach ($friends as $friend) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <?php
                            $avatar = !empty($friend['displayPicRaw']) ? $friend['displayPicRaw'] : '../img/default_avatar.jpg';
                            $addedDate = new DateTime($friend['addedDateTimeUtc']);
                            $formattedDate = $addedDate->format('d/m/Y');
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo $friend['displayName']; ?>" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($friend['gamertag']); ?></p>
                            <p class="text-sm text-gray-400">Amigo desde: <?php echo $formattedDate; ?></p>
                            <p class="text-sm text-gray-400">Gamerscore: <?php echo number_format($friend['gamerScore'], 0, ',', '.'); ?>
                                <img src="../img/gs.png" alt="Gamerscore Icon" class="inline-block w-4 h-4">
                            </p>
                            <p class="text-sm text-gray-400"><?php echo $friend['presenceText']; ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Você não tem amigos adicionados no momento.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>