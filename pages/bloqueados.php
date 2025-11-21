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
    
    $endpoint = "friends/blocked";
    $response = openXBLRequest($endpoint);
    
    if ($response && isset($response['users']) && is_array($response['users'])) {
        $blockedFriends = $response['users'];
    } else {
        $blockedFriends = [];
    }
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Amigos Bloqueados</h1>
    <?php if (!empty($blockedFriends)) : ?>
        <ul>
            <?php foreach ($blockedFriends as $blockedFriend) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <?php
                            $avatar = !empty($blockedFriend['displayPicRaw']) ? $blockedFriend['displayPicRaw'] : '../img/default_avatar.jpg';
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo $blockedFriend['gamertag']; ?>" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($blockedFriend['gamertag']); ?></p>
                            <p class="text-sm text-gray-400">Status: Bloqueado</p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Você não tem amigos bloqueados no momento.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>