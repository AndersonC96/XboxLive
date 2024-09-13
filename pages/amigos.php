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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($friends as $friend) : ?>
                <div class="bg-gray-800 text-white rounded-lg shadow-lg overflow-hidden">
                    <div class="flex">
                        <?php
                            $avatar = !empty($friend['displayPicRaw']) ? $friend['displayPicRaw'] : '../img/default_avatar.jpg';
                            $addedDate = new DateTime($friend['addedDateTimeUtc']);
                            $formattedDate = $addedDate->format('d/m/Y');
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo $friend['displayName']; ?>" class="w-1/3 h-auto object-cover">
                        <div class="p-4 flex-1">
                            <h2 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($friend['gamertag']); ?></h2>
                            <p class="text-sm text-gray-400">Amigo desde: <?php echo $formattedDate; ?></p>
                            <p class="text-sm text-gray-400 flex items-center">
                                Gamerscore: <?php echo number_format($friend['gamerScore'], 0, ',', '.'); ?>
                                <img src="../img/gs.png" alt="Gamerscore Icon" class="w-4 h-4 ml-2">
                            </p>
                            <p class="text-xs text-gray-400 mt-4"><?php echo $friend['presenceText']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Você não tem amigos adicionados no momento.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>