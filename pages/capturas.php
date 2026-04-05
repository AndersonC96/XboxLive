<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$user = AuthService::user();
$xuid = $user['xuid'] ?? null;

$api = new OpenXBLService();
$screenshots = $api->getScreenshots($xuid);
$clips = $api->getGameClips($xuid);

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="max-w-7xl mx-auto px-8 py-12 animate-fade-in">
    <header class="mb-12">
        <h1 class="text-4xl font-black text-white tracking-tight mb-2">Suas Capturas</h1>
        <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">Screenshots e clipes salvos na Xbox Live</p>
    </header>

    <div class="space-y-16">
        <!-- Screenshots -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black flex items-center gap-3">
                    <i class="fas fa-camera text-xbox-green"></i> Screenshots
                </h3>
                <span class="text-xs font-bold text-gray-700"><?php echo count($screenshots ?? []); ?> capturas</span>
            </div>

            <?php if (!empty($screenshots)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($screenshots as $shot): ?>
                        <div class="glass-card group rounded-2xl overflow-hidden border-white/5 hover:border-xbox-green/40 transition-all">
                            <div class="aspect-video relative overflow-hidden bg-gray-900">
                                <img src="<?php echo $shot['thumbnails'][0]['uri'] ?? $shot['screenshotUris'][0]['uri']; ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                    <a href="<?php echo $shot['screenshotUris'][0]['uri']; ?>" target="_blank" 
                                       class="w-10 h-10 rounded-full bg-white text-xbox-dark flex items-center justify-center hover:bg-xbox-green hover:text-white transition-all">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                    <a href="<?php echo $shot['screenshotUris'][0]['uri']; ?>" download 
                                       class="w-10 h-10 rounded-full bg-white text-xbox-dark flex items-center justify-center hover:bg-xbox-green hover:text-white transition-all">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-white text-sm truncate mb-1"><?php echo htmlspecialchars($shot['titleName']); ?></h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                    <?php echo date('d/m/Y', strtotime($shot['dateTaken'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="py-20 text-center glass-card rounded-3xl border-dashed opacity-50">
                    <i class="fas fa-image text-4xl mb-4"></i>
                    <p class="font-bold text-gray-500">Nenhuma captura de tela encontrada.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Game Clips -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black flex items-center gap-3">
                    <i class="fas fa-video text-xbox-green"></i> Clipes de Jogo
                </h3>
                <span class="text-xs font-bold text-gray-700"><?php echo count($clips ?? []); ?> clipes</span>
            </div>

            <?php if (!empty($clips)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($clips as $clip): ?>
                        <div class="glass-card group rounded-2xl overflow-hidden border-white/5 hover:border-xbox-green/40 transition-all">
                            <div class="aspect-video relative overflow-hidden bg-gray-900">
                                <video poster="<?php echo $clip['thumbnails'][0]['uri'] ?? ''; ?>" 
                                       class="w-full h-full object-cover">
                                    <source src="<?php echo $clip['gameClipUris'][0]['uri']; ?>" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center group-hover:bg-black/20 transition-all">
                                    <button onclick="this.parentElement.previousElementSibling.play(); this.parentElement.style.display='none';" 
                                            class="w-14 h-14 rounded-full bg-xbox-green text-white flex items-center justify-center shadow-lg shadow-xbox-green/20 hover:scale-110 transition-transform">
                                        <i class="fas fa-play ml-1"></i>
                                    </button>
                                </div>
                                <div class="absolute top-4 right-4">
                                    <span class="px-2 py-1 rounded bg-black/60 backdrop-blur-md text-[10px] font-black text-white">
                                        <?php echo $clip['durationInSeconds']; ?>s
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-white text-sm truncate mb-1"><?php echo htmlspecialchars($clip['titleName']); ?></h4>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                        <?php echo date('d/m/Y', strtotime($clip['dateRecorded'])); ?>
                                    </p>
                                </div>
                                <a href="<?php echo $clip['gameClipUris'][0]['uri']; ?>" download 
                                   class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 text-gray-400 flex items-center justify-center hover:bg-xbox-green hover:text-white transition-all">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="py-20 text-center glass-card rounded-3xl border-dashed opacity-50">
                    <i class="fas fa-film text-4xl mb-4"></i>
                    <p class="font-bold text-gray-500">Nenhum clipe de jogo encontrado.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
