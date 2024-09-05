<?php
    session_start();
    require '../config/db.php';
    require '../config/api.php';
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT xuid FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['xuid']) {
        $xuid = $user['xuid'];
        $endpoint = "account";
        $response = openXBLRequest($endpoint);
        if (isset($response['profileUsers'][0]['settings'])) {
            $settings = $response['profileUsers'][0]['settings'];
            foreach ($settings as $setting) {
                if ($setting['id'] === 'GameDisplayPicRaw') {
                    $gamerpic = $setting['value'];
                }
                if ($setting['id'] === 'Gamertag') {
                    $gamertag = $setting['value'];
                }
            }
        } else {
            $gamerpic = 'default_avatar.jpg';
            $gamertag = 'Usuário';
        }
    } else {
        $gamerpic = 'default_avatar.jpg';
        $gamertag = 'Usuário';
    }
?>
<nav class="bg-gray-800 p-4">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="dashboard.php">
                <img src="../img/logo2.png" alt="Xbox Logo" class="w-10 h-10">
            </a>
            <a href="dashboard.php" class="text-white">Conta</a>
            <a href="#" class="text-white">Conquistas</a>
            <a href="#" class="text-white">Atividade</a>
            <a href="#" class="text-white">Clubes</a>
            <a href="#" class="text-white">Conversas</a>
            <a href="#" class="text-white">DVR</a>
            <a href="#" class="text-white">Amigos</a>
            <a href="#" class="text-white">Gamepass</a>
            <a href="#" class="text-white">Loja</a>
            <a href="#" class="text-white">Jogador</a>
            <a href="#" class="text-white">Sessão</a>
            <a href="#" class="text-white">Grupo</a>
        </div>
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Buscar" class="px-4 py-2 rounded-lg">
            <img src="<?php echo $gamerpic; ?>" alt="Profile" class="w-10 h-10 rounded-full">
            <a href="logout.php" class="text-white">Sair</a>
        </div>
    </div>
</nav>