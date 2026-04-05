<?php

use Anderson\XboxLive\Services\OpenXBLService;
use Anderson\XboxLive\Services\AuthService;

$openXBL = new OpenXBLService();
$response = $openXBL->get("account");

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

$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="glass sticky top-0 z-50 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Brand -->
            <div class="flex items-center gap-8">
                <a href="dashboard.php" class="flex items-center gap-3 group">
                    <img src="../img/logo2.png" alt="Xbox" class="w-10 h-10 group-hover:scale-110 transition-transform duration-300">
                    <span class="hidden md:block font-bold text-xl tracking-tight text-white">XBOX LIVE</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-1">
                    <div class="relative group">
                        <button class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors flex items-center gap-2">
                            <span>Social</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div class="absolute top-full left-0 mt-1 w-48 glass rounded-xl overflow-hidden hidden group-hover:block animate-fade-in">
                            <a href="amigos.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-users w-4"></i> Amigos
                            </a>
                            <a href="bloqueados.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-ban w-4"></i> Bloqueados
                            </a>
                            <a href="recentes.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-history w-4"></i> Recentes
                            </a>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors flex items-center gap-2">
                            <span>Game Pass</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div class="absolute top-full left-0 mt-1 w-56 glass rounded-xl overflow-hidden hidden group-hover:block animate-fade-in">
                            <a href="todos_os_jogos.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-list w-4"></i> Todos os Jogos
                            </a>
                            <a href="ea_play.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-play-circle w-4"></i> EA Play
                            </a>
                            <a href="gamepass_pc.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-laptop w-4"></i> PC Gamepass
                            </a>
                        </div>
                    </div>

                    <a href="conquistas.php" class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">Conquistas</a>
                    
                    <div class="relative group">
                        <button class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors flex items-center gap-2">
                            <span>Loja</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div class="absolute top-full left-0 mt-1 w-56 glass rounded-xl overflow-hidden hidden group-hover:block animate-fade-in">
                            <a href="mais_jogados.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-fire w-4"></i> Mais Jogados
                            </a>
                            <a href="promocao.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-tags w-4"></i> Promoções
                            </a>
                            <a href="novos_jogos.php" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                <i class="fas fa-plus-circle w-4"></i> Novos Jogos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile & Search -->
            <div class="flex items-center gap-4">
                <form action="search.php" method="GET" class="hidden md:flex items-center bg-white/5 border border-white/10 rounded-full px-4 py-1.5 focus-within:border-xbox-green transition-all">
                    <i class="fas fa-search text-gray-500 text-sm"></i>
                    <input type="text" name="gamertag_search" placeholder="Buscar Gamertag" class="bg-transparent border-none outline-none px-3 py-1 text-sm w-40 lg:w-48 placeholder:text-gray-600">
                </form>

                <div class="relative group">
                    <button class="flex items-center gap-3 pl-1 pr-3 py-1 rounded-full hover:bg-white/5 transition-all">
                        <div class="relative">
                            <img src="<?php echo $gamerpic; ?>" alt="Profile" class="w-10 h-10 rounded-full border-2 border-xbox-green shadow-[0_0_15px_rgba(16,124,16,0.5)]">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-xbox-dark rounded-full"></div>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-bold leading-none"><?php echo htmlspecialchars($gamertag); ?></p>
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Online</span>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] opacity-50 hidden sm:block"></i>
                    </button>
                    
                    <div class="absolute top-full right-0 mt-2 w-48 glass rounded-xl overflow-hidden hidden group-hover:block animate-fade-in shadow-2xl">
                        <div class="nav-dropdown-item font-black uppercase text-[10px] tracking-widest text-xbox-green/50 px-4 py-2 border-b border-white/5 mb-1">
                            Conta
                        </div>
                        <a href="perfil.php" class="nav-dropdown-item flex items-center justify-between group/link px-4 py-3 hover:bg-white/5 transition-colors">
                            Meu Perfil
                            <i class="fas fa-id-card text-[10px] text-gray-700 group-hover/link:text-xbox-green transition-colors"></i>
                        </a>
                        <a href="logout.php" class="nav-dropdown-item flex items-center justify-between group/link px-4 py-3 hover:bg-red-500/20 text-red-400 transition-colors">
                            Sair
                            <i class="fas fa-sign-out-alt text-[10px] text-gray-700 group-hover/link:text-red-500 transition-colors"></i>
                        </a>
                    </div>
                </div>

                <!-- Mobile menu toggle -->
                <button class="lg:hidden p-2 text-gray-400 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>