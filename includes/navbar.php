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
            <div class="relative">
                <button id="dropdown-amigos-btn" class="text-white hover:text-gray-400 focus:outline-none">
                    <i class="fas fa-user-friends mr-2"></i> Amigos
                    <i class="fas fa-chevron-down ml-1"></i>
                </button>
                <div id="dropdown-amigos-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg mt-2">
                    <a href="../pages/amigos.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-users mr-2"></i> Amigos
                    </a>
                    <a href="../pages/bloqueados.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-ban mr-2"></i> Bloqueados
                    </a>
                    <a href="../pages/recentes.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-history mr-2"></i> Recentes
                    </a>
                </div>
            </div>
            <div class="relative">
                <button id="dropdown-activity-btn" class="text-white flex items-center hover:text-gray-400 focus:outline-none">
                    <i class="fas fa-stream mr-2"></i> Atividade
                    <i class="fas fa-chevron-down ml-1"></i>
                </button>
                <div id="dropdown-activity-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg mt-2">
                    <a href="../pages/feed.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-rss mr-2"></i> Feed
                    </a>
                    <a href="../pages/historico.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-history mr-2"></i> Histórico
                    </a>
                </div>
            </div>
            <a href="conquistas.php" class="text-white flex items-center hover:text-gray-400">
                <i class="fas fa-trophy mr-2"></i> Conquistas
            </a>
            <div class="relative">
                <button id="dropdown-gamepass-btn" class="text-white flex items-center hover:text-gray-400 focus:outline-none">
                    <i class="fas fa-gamepad mr-2"></i> Gamepass
                    <i class="fas fa-chevron-down ml-1"></i>
                </button>
                <div id="dropdown-gamepass-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg mt-2">
                    <a href="../pages/todos_os_jogos.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-list mr-2"></i> Todos os Jogos
                    </a>
                    <a href="../pages/ea_play.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-play-circle mr-2"></i> EA Play
                    </a>
                    <a href="../pages/jogos_sem_controle.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-gamepad mr-2"></i> Jogos sem Controle
                    </a>
                    <a href="../pages/gamepass_pc.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-laptop mr-2"></i> PC Gamepass
                    </a>
                </div>
            </div>
            <div class="relative">
                <button id="dropdown-loja-btn" class="text-white flex items-center hover:text-gray-400 focus:outline-none">
                    <i class="fas fa-store mr-2"></i> Loja
                    <i class="fas fa-chevron-down ml-1"></i>
                </button>
                <div id="dropdown-loja-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg mt-2">
                    <a href="../pages/em_breve.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-hourglass-start mr-2"></i> Em Breve
                    </a>
                    <a href="../pages/mais_jogados.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-fire mr-2"></i> Mais Jogados
                    </a>
                    <a href="../pages/melhores_avaliados.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-star mr-2"></i> Melhores Avaliados
                    </a>
                    <a href="../pages/novos_jogos.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i> Novos Jogos
                    </a>
                    <a href="../pages/populares_gratis.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-gift mr-2"></i> Populares Grátis
                    </a>
                    <a href="../pages/populares_pagos.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-dollar-sign mr-2"></i> Populares Pagos
                    </a>
                    <a href="../pages/promocao.php" class="block px-4 py-2 hover:bg-gray-600 flex items-center">
                        <i class="fas fa-tags mr-2"></i> Promoção
                    </a>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <form action="search.php" method="GET" class="flex items-center">
                <input type="text" name="gamertag_search" placeholder="Buscar Gamertag" class="px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400" required>
                <button type="submit" class="ml-2 bg-green-500 text-white px-4 py-2 rounded-lg">Buscar</button>
            </form>
            <button class="focus:outline-none">
                <img src="<?php echo $gamerpic; ?>" alt="Profile" class="w-10 h-10 rounded-full border-2 border-green-500" id="user-menu-button">
            </button>
        </div>
    </div>
</nav>
<script>
    document.getElementById('dropdown-amigos-btn').addEventListener('click', function() {
        document.getElementById('dropdown-amigos-menu').classList.toggle('hidden');
    });
    document.getElementById('dropdown-activity-btn').addEventListener('click', function() {
        document.getElementById('dropdown-activity-menu').classList.toggle('hidden');
    });
    document.getElementById('dropdown-gamepass-btn').addEventListener('click', function() {
        document.getElementById('dropdown-gamepass-menu').classList.toggle('hidden');
    });
    document.getElementById('dropdown-loja-btn').addEventListener('click', function() {
        document.getElementById('dropdown-loja-menu').classList.toggle('hidden');
    });
    document.getElementById('user-menu-button').addEventListener('click', function() {
        document.getElementById('user-menu').classList.toggle('hidden');
    });
</script>