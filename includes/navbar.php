<?php
    require '../config/db.php';
    require_once '../config/api.php';

    // Obter dados da conta diretamente da API (não depende de xuid no banco)
    $endpoint = "account";
    $response = openXBLRequest($endpoint);

    $gamerpic = '../img/default_avatar.jpg';
    $gamertag = 'Usuário';

    if ($response && isset($response['profileUsers'][0]['settings'])) {
        $settings = $response['profileUsers'][0]['settings'];
        foreach ($settings as $setting) {
            if ($setting['id'] === 'GameDisplayPicRaw') {
                $gamerpic = $setting['value'];
            }
            if ($setting['id'] === 'Gamertag') {
                $gamertag = $setting['value'];
            }
        }
    }
?>
<nav class="liquid-nav relative">
    <div class="nav-glass-overlay"></div>
    <div class="nav-grid"></div>
    <div class="nav-orb nav-orb-left"></div>
    <div class="nav-orb nav-orb-right"></div>

    <div class="relative z-10 flex justify-between items-center max-w-7xl mx-auto px-4">
        <div class="flex items-center space-x-4">
            <a href="dashboard.php" class="nav-brand flex items-center space-x-3">
                <span class="nav-brand-icon grid place-items-center">
                    <img src="../img/logo2.png" alt="Xbox Logo" class="w-10 h-10">
                </span>
                <span class="nav-brand-text">Xbox Live</span>
            </a>

            <div class="relative nav-item">
                <button id="dropdown-amigos-btn" class="nav-link">
                    <i class="fas fa-user-friends mr-2"></i> Amigos
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>
                <div id="dropdown-amigos-menu" class="nav-dropdown hidden">
                    <a href="../pages/amigos.php" class="nav-dropdown-item">
                        <i class="fas fa-users mr-2"></i> Amigos
                    </a>
                    <a href="../pages/bloqueados.php" class="nav-dropdown-item">
                        <i class="fas fa-ban mr-2"></i> Bloqueados
                    </a>
                    <a href="../pages/recentes.php" class="nav-dropdown-item">
                        <i class="fas fa-history mr-2"></i> Recentes
                    </a>
                </div>
            </div>

            <div class="relative nav-item">
                <button id="dropdown-activity-btn" class="nav-link">
                    <i class="fas fa-stream mr-2"></i> Atividade
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>
                <div id="dropdown-activity-menu" class="nav-dropdown hidden">
                    <a href="../pages/feed.php" class="nav-dropdown-item">
                        <i class="fas fa-rss mr-2"></i> Feed
                    </a>
                    <a href="../pages/historico.php" class="nav-dropdown-item">
                        <i class="fas fa-history mr-2"></i> Histórico
                    </a>
                </div>
            </div>

            <a href="conquistas.php" class="nav-link">
                <i class="fas fa-trophy mr-2"></i> Conquistas
            </a>

            <div class="relative nav-item">
                <button id="dropdown-gamepass-btn" class="nav-link">
                    <i class="fas fa-gamepad mr-2"></i> Gamepass
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>
                <div id="dropdown-gamepass-menu" class="nav-dropdown hidden">
                    <a href="../pages/todos_os_jogos.php" class="nav-dropdown-item">
                        <i class="fas fa-list mr-2"></i> Todos os Jogos
                    </a>
                    <a href="../pages/ea_play.php" class="nav-dropdown-item">
                        <i class="fas fa-play-circle mr-2"></i> EA Play
                    </a>
                    <a href="../pages/jogos_sem_controle.php" class="nav-dropdown-item">
                        <i class="fas fa-gamepad mr-2"></i> Jogos sem Controle
                    </a>
                    <a href="../pages/gamepass_pc.php" class="nav-dropdown-item">
                        <i class="fas fa-laptop mr-2"></i> PC Gamepass
                    </a>
                </div>
            </div>

            <div class="relative nav-item">
                <button id="dropdown-loja-btn" class="nav-link">
                    <i class="fas fa-store mr-2"></i> Loja
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>
                <div id="dropdown-loja-menu" class="nav-dropdown hidden">
                    <a href="../pages/em_breve.php" class="nav-dropdown-item">
                        <i class="fas fa-hourglass-start mr-2"></i> Em Breve
                    </a>
                    <a href="../pages/mais_jogados.php" class="nav-dropdown-item">
                        <i class="fas fa-fire mr-2"></i> Mais Jogados
                    </a>
                    <a href="../pages/melhores_avaliados.php" class="nav-dropdown-item">
                        <i class="fas fa-star mr-2"></i> Melhores Avaliados
                    </a>
                    <a href="../pages/novos_jogos.php" class="nav-dropdown-item">
                        <i class="fas fa-plus-circle mr-2"></i> Novos Jogos
                    </a>
                    <a href="../pages/populares_gratis.php" class="nav-dropdown-item">
                        <i class="fas fa-gift mr-2"></i> Populares Grátis
                    </a>
                    <a href="../pages/populares_pagos.php" class="nav-dropdown-item">
                        <i class="fas fa-dollar-sign mr-2"></i> Populares Pagos
                    </a>
                    <a href="../pages/promocao.php" class="nav-dropdown-item">
                        <i class="fas fa-tags mr-2"></i> Promoção
                    </a>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <form action="search.php" method="GET" class="nav-search">
                <i class="fas fa-search text-green-200"></i>
                <input type="text" name="gamertag_search" placeholder="Buscar Gamertag" class="nav-search-input" required>
                <button type="submit" class="nav-search-button">Buscar</button>
            </form>
            <button class="focus:outline-none nav-profile" id="user-menu-button">
                <div class="nav-profile-ring"></div>
                <img src="<?php echo $gamerpic; ?>" alt="Profile" class="nav-profile-img">
                <span class="nav-profile-name"><?php echo htmlspecialchars($gamertag); ?></span>
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
</script>
