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
$response = $api->get("achievements");
$games = $response['titles'] ?? [];

include('../includes/header.php');
include('../includes/navbar.php');

function getBoxArtUrl($game) {
    if (isset($game['images'])) {
        foreach ($game['images'] as $image) {
            if ($image['type'] === 'BoxArt') return $image['url'];
        }
    }
    return '../img/default_game.jpg';
}
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Seu Progresso</span>
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4">Conquistas</h1>
        <p class="text-gray-500 font-medium">Acompanhe sua jornada em todos os títulos do ecossistema Xbox.</p>
    </header>

    <?php if (!empty($games)) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($games as $game) : ?>
                <?php
                    $name = $game['name'] ?? 'Título Desconhecido';
                    $currentGs = $game['achievement']['currentGamerscore'] ?? 0;
                    $totalGs = $game['achievement']['totalGamerscore'] ?? 0;
                    $progress = $game['achievement']['progressPercentage'] ?? 0;
                    $boxArt = getBoxArtUrl($game);
                ?>
                <article class="glass-card group rounded-3xl p-6 hover:border-xbox-green/40 transition-all">
                    <div class="flex gap-6 mb-6">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden shadow-2xl flex-shrink-0">
                            <img src="<?php echo htmlspecialchars($boxArt); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Game">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-white text-lg leading-tight mb-2 truncate"><?php echo htmlspecialchars($name); ?></h3>
                            <div class="flex items-center gap-2 mb-4">
                                <img src="../img/gs.png" class="w-4 h-4" alt="GS">
                                <span class="text-sm font-bold text-xbox-green"><?php echo number_format($currentGs, 0, ',', '.'); ?></span>
                                <span class="text-xs text-gray-500">/ <?php echo number_format($totalGs, 0, ',', '.'); ?></span>
                            </div>
                            
                            <div class="relative pt-1">
                                <div class="flex mb-2 items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-widest inline-block py-1 px-2 rounded-full text-xbox-green bg-xbox-green/10">
                                            Progresso
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold text-gray-500">
                                            <?php echo $progress; ?>%
                                        </span>
                                    </div>
                                </div>
                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-white/5">
                                    <div style="width:<?php echo $progress; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-xbox-green shadow-[0_0_10px_rgba(16,124,16,0.3)]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="py-32 text-center glass-card rounded-3xl border-dashed">
            <i class="fas fa-trophy text-6xl text-gray-800 mb-6"></i>
            <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">Nenhuma conquista registrada</p>
        </div>
    <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>