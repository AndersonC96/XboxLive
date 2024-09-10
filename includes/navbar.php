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
                <button id="dropdown-amigos" class="text-white hover:text-gray-400 focus:outline-none">
                    Amigos
                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="amigos-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg">
                    <a href="../pages/amigos.php" class="block px-4 py-2 hover:bg-gray-600">Amigos</a>
                    <a href="../pages/bloqueados.php" class="block px-4 py-2 hover:bg-gray-600">Bloqueados</a>
                    <a href="../pages/recentes.php" class="block px-4 py-2 hover:bg-gray-600">Recentes</a>
                    <a href="../pages/status.php" class="block px-4 py-2 hover:bg-gray-600">Status</a>
                </div>
            </div>
            <div class="relative">
                <button id="dropdown-activity" class="text-white hover:text-gray-400 focus:outline-none">
                    Atividade
                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="activity-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg">
                    <a href="../pages/feed.php" class="block px-4 py-2 hover:bg-gray-600">Feed</a>
                    <a href="../pages/historico.php" class="block px-4 py-2 hover:bg-gray-600">Histórico</a>
                </div>
            </div>
            <div class="relative">
                <button id="dropdown-clubes" class="text-white hover:text-gray-400 focus:outline-none">
                    Clubes
                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="clubes-menu" class="hidden absolute bg-gray-700 text-white rounded-lg shadow-lg">
                    <a href="../pages/procurar_clube.php" class="block px-4 py-2 hover:bg-gray-600">Procurar clube</a>
                    <a href="../pages/recomendacoes.php" class="block px-4 py-2 hover:bg-gray-600">Recomendações</a>
                </div>
            </div>
            <a href="#" class="text-white">Conquistas</a>
            <a href="#" class="text-white">Conversas</a>
            <a href="#" class="text-white">DVR</a>
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
    document.getElementById('dropdown-activity').addEventListener('click', function() {
        var menu = document.getElementById('activity-menu');
        menu.classList.toggle('hidden');
    });
    document.getElementById('dropdown-clubes').addEventListener('click', function() {
        var menu = document.getElementById('clubes-menu');
        menu.classList.toggle('hidden');
    });
    document.getElementById('dropdown-amigos').addEventListener('click', function() {
        var menu = document.getElementById('amigos-menu');
        menu.classList.toggle('hidden');
    });
    document.getElementById('user-menu-button').addEventListener('click', function() {
        var menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    });
</script>