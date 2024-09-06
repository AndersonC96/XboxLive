<?php
    require '../config/db.php';
    require_once '../config/api.php';
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
            $gamerpic = '../img/default_avatar.jpg';
            $gamertag = 'Usuário';
        }
    } else {
        $gamerpic = '../img/default_avatar.jpg';
        $gamertag = 'Usuário';
    }
?>
<nav class="bg-gray-800 p-4">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="dashboard.php">
                <img src="../img/logo2.png" alt="Xbox Logo" class="w-10 h-10">
            </a>
            <a href="dashboard.php" class="text-white">Home</a>
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
        <form action="search.php" method="GET" class="flex">
            <input type="text" name="gamertag_search" placeholder="Buscar Gamertag" class="px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400" required>
            <button type="submit" class="ml-2 bg-green-500 text-white px-4 py-2 rounded-lg">Buscar</button>
        </form>
        <div class="relative ml-4">
            <button class="focus:outline-none">
                <img src="<?php echo $gamerpic; ?>" alt="Profile" class="w-10 h-10 rounded-full border-2 border-green-500" id="user-menu-button">
            </button>
            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg py-2 z-50">
                <a href="../pages/gerar_gamertag.php" class="block px-4 py-2 text-sm hover:bg-gray-200">Gerar Gamertag</a>
                <a href="../pages/alerts.php" class="block px-4 py-2 text-sm hover:bg-gray-200">Notificações</a>
                <a href="../pages/presence.php" class="block px-4 py-2 text-sm hover:bg-gray-200">Status</a>
                <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-200">Sair</a>
            </div>
        </div>
    </div>
</nav>
<script>
    document.getElementById('user-menu-button').addEventListener('click', function() {
        var menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    });
</script>