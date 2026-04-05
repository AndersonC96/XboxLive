<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$api = new OpenXBLService();
$response = $api->get("friends");
$friends = $response['people'] ?? [];

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Comunidade</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white font-black">Lista de Amigos</h1>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative w-full md:w-80 group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-xbox-green transition-colors"></i>
                <input type="text" id="filterGamertag" placeholder="Buscar por Gamertag..." 
                    class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all">
            </div>
        </div>
    </header>

    <?php if (!empty($friends)) : ?>
        <div id="friendsList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($friends as $friend) : ?>
                <?php
                    $avatar = !empty($friend['displayPicRaw']) ? $friend['displayPicRaw'] : '../img/default_avatar.jpg';
                    $status = $friend['presenceState'] ?? 'Offline';
                    $isOnline = $status === 'Online';
                ?>
                <article class="glass-card group rounded-3xl p-6 hover:border-xbox-green/40 transition-all friend-card" 
                    data-gamertag="<?php echo htmlspecialchars(strtolower($friend['gamertag'])); ?>">
                    
                    <div class="flex flex-col items-center text-center">
                        <div class="relative mb-4">
                            <img src="<?php echo $avatar; ?>" alt="Avatar" class="w-20 h-20 rounded-2xl border-2 <?php echo $isOnline ? 'border-xbox-green shadow-[0_0_15px_rgba(16,124,16,0.4)]' : 'border-white/10'; ?> object-cover">
                            <?php if ($isOnline): ?>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-4 border-[#121212] rounded-full"></div>
                            <?php endif; ?>
                        </div>

                        <h3 class="font-bold text-white text-lg mb-1 truncate w-full"><?php echo htmlspecialchars($friend['gamertag']); ?></h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-4"><?php echo htmlspecialchars($friend['presenceText'] ?? 'Offline'); ?></p>
                        
                        <div class="w-full pt-4 border-t border-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <img src="../img/gs.png" class="w-3.5 h-3.5" alt="GS">
                                <span class="text-xs font-bold text-xbox-green"><?php echo number_format($friend['gamerScore'], 0, ',', '.'); ?></span>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Ver Perfil</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="py-24 text-center glass-card rounded-3xl border-dashed">
            <i class="fas fa-users-slash text-6xl text-gray-800 mb-6"></i>
            <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">Nenhum amigo encontrado</p>
            <p class="text-sm text-gray-700 mt-2">Parece que sua lista está vazia no momento.</p>
        </div>
    <?php endif; ?>
</main>

<script>
    document.getElementById('filterGamertag').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.friend-card').forEach(card => {
            const gamertag = card.getAttribute('data-gamertag');
            card.style.display = gamertag.includes(term) ? 'block' : 'none';
        });
    });
</script>

<?php include('../includes/footer.php'); ?>