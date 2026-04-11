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

// Busca de dados com tratamento defensivo
$allScreenshots = $api->getScreenshots($xuid) ?? [];
$allClips = $api->getGameClips($xuid) ?? [];

// Configuração de Paginação
$itemsPerPage = 10; // Reduzido para 10 para encaixar bem em grid 5

// Paginação: Screenshots (spage)
$sTotalItems = count($allScreenshots);
$sTotalPages = ceil($sTotalItems / $itemsPerPage);
$sCurrentPage = isset($_GET['spage']) ? (int)$_GET['spage'] : 1;
if ($sCurrentPage < 1) $sCurrentPage = 1;
$sOffset = ($sCurrentPage - 1) * $itemsPerPage;
$screenshots = array_slice($allScreenshots, $sOffset, $itemsPerPage);

// Paginação: Clipes (cpage)
$cTotalItems = count($allClips);
$cTotalPages = ceil($cTotalItems / $itemsPerPage);
$cCurrentPage = isset($_GET['cpage']) ? (int)$_GET['cpage'] : 1;
if ($cCurrentPage < 1) $cCurrentPage = 1;
$cOffset = ($cCurrentPage - 1) * $itemsPerPage;
$clips = array_slice($allClips, $cOffset, $itemsPerPage);

// URLs Base para manter estado mútuo
$sBaseUrl = 'capturas.php?cpage=' . $cCurrentPage;
$cBaseUrl = 'capturas.php?spage=' . $sCurrentPage;

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in space-y-24">
    <!-- Header Premium -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block text-shadow-sm">Social</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white italic drop-shadow-lg uppercase">Capturas e Clipes</h1>
            <p class="text-gray-500 font-medium max-w-xl">Gerencie seus melhores momentos salvos na nuvem da Xbox Live com alta fidelidade.</p>
        </div>
    </header>

    <!-- SECTION: Screenshots (Grade 5) -->
    <section id="screenshots-section">
        <div class="flex items-center justify-between mb-10 border-b border-white/5 pb-6">
            <h3 class="text-2xl font-black flex items-center gap-4 italic uppercase tracking-tighter">
                <i class="fas fa-camera text-xbox-green shadow-green-glow"></i> Screenshots
            </h3>
            <span class="px-4 py-1 rounded-full bg-white/5 text-[10px] font-black text-gray-500 uppercase tracking-widest border border-white/5">
                <?php echo $sTotalItems; ?> TOTAL
            </span>
        </div>

        <?php if (!empty($screenshots)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                <?php foreach ($screenshots as $shot): ?>
                    <?php
                        $sThumb = $shot['thumbnails'][0]['uri'] ?? ($shot['screenshotUris'][0]['uri'] ?? '../img/placeholder.png');
                        $sFull = $shot['screenshotUris'][0]['uri'] ?? $sThumb;
                        $sTitle = $shot['titleName'] ?? 'Jogo Desconhecido';
                        $sDate = isset($shot['dateTaken']) ? date('d M Y', strtotime($shot['dateTaken'])) : 'N/A';
                    ?>
                    <article class="glass-card group rounded-2xl overflow-hidden hover:border-xbox-green/40 transition-all">
                        <div class="aspect-video relative overflow-hidden bg-xbox-surface">
                            <img src="<?php echo $sThumb; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                            
                            <!-- Glass-Action Overlay -->
                            <div class="absolute inset-0 bg-xbox-dark/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4">
                                <a href="<?php echo $sFull; ?>" target="_blank" title="Expandir"
                                   class="w-12 h-12 rounded-2xl bg-white text-xbox-dark flex items-center justify-center hover:bg-xbox-green hover:text-white transition-all transform scale-75 group-hover:scale-100 duration-300 shadow-2xl">
                                    <i class="fas fa-expand"></i>
                                </a>
                                <a href="<?php echo $sFull; ?>" download title="Baixar"
                                   class="w-12 h-12 rounded-2xl bg-white text-xbox-dark flex items-center justify-center hover:bg-xbox-green hover:text-white transition-all transform scale-75 group-hover:scale-100 duration-300 shadow-2xl delay-75">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-4 bg-gradient-to-b from-transparent to-xbox-dark/20">
                            <h4 class="font-bold text-white text-[11px] truncate mb-1 uppercase tracking-tight"><?php echo htmlspecialchars($sTitle); ?></h4>
                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest italic"><?php echo $sDate; ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-16">
                <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($sCurrentPage, $sTotalPages, $sBaseUrl, 'spage'); ?>
            </div>

        <?php else: ?>
            <div class="py-24 text-center glass-card rounded-[3rem] border-white/5 opacity-80 relative overflow-hidden group">
                <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
                    <i class="fas fa-camera text-[20rem] rotate-12 group-hover:rotate-0 transition-transform duration-700"></i>
                </div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-[2rem] bg-white/5 flex items-center justify-center text-gray-600 mb-6 border border-white/10">
                        <i class="fas fa-image text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2 italic">LENDA SILENCIOSA...</h3>
                    <p class="text-gray-600 font-medium max-w-sm mx-auto text-sm">Capture seus melhores momentos no console para exibi-los aqui.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- SECTION: Clipes de Jogo (Grade 4) -->
    <section id="clips-section">
        <div class="flex items-center justify-between mb-10 border-b border-white/5 pb-6">
            <h3 class="text-2xl font-black flex items-center gap-4 italic uppercase tracking-tighter">
                <i class="fas fa-video text-xbox-green shadow-green-glow"></i> Clipes de Jogo
            </h3>
            <span class="px-4 py-1 rounded-full bg-white/5 text-[10px] font-black text-gray-500 uppercase tracking-widest border border-white/5">
                <?php echo $cTotalItems; ?> TOTAL
            </span>
        </div>

        <?php if (!empty($clips)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($clips as $clip): ?>
                    <?php
                        $cThumb = $clip['thumbnails'][0]['uri'] ?? '';
                        $cUri = $clip['gameClipUris'][0]['uri'] ?? '';
                        $cTitle = $clip['titleName'] ?? 'Jogo Desconhecido';
                        $cDate = isset($clip['dateRecorded']) ? date('d M Y', strtotime($clip['dateRecorded'])) : 'N/A';
                        $cDuration = $clip['durationInSeconds'] ?? 0;
                    ?>
                    <article class="glass-card group rounded-3xl overflow-hidden hover:border-xbox-green/40 transition-all bg-xbox-surface/30">
                        <div class="aspect-video relative overflow-hidden bg-xbox-dark shadow-inner">
                            <video poster="<?php echo $cThumb; ?>" class="w-full h-full object-cover">
                                <source src="<?php echo $cUri; ?>" type="video/mp4">
                            </video>
                            
                            <!-- Video Controls Overlay -->
                            <div class="absolute inset-0 bg-xbox-dark/40 flex items-center justify-center group-hover:bg-xbox-dark/20 transition-all duration-300">
                                <?php if ($cUri): ?>
                                <button onclick="const v = this.parentElement.previousElementSibling; if(v.paused){v.play(); this.style.opacity=0;}else{v.pause(); this.style.opacity=1;}" 
                                        class="w-16 h-16 rounded-full bg-xbox-green/90 text-white flex items-center justify-center shadow-2xl hover:scale-110 transition-transform active:scale-95 group-hover:opacity-100 opacity-60">
                                    <i class="fas fa-play ml-1"></i>
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Duration Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="bg-xbox-dark/80 backdrop-blur-md text-white text-[9px] font-black px-2.5 py-1 rounded-lg border border-white/10 shadow-xl">
                                    <?php echo sprintf('%02d:%02d', floor($cDuration / 60), $cDuration % 60); ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="font-bold text-white text-[12px] truncate mb-1 uppercase tracking-tighter"><?php echo htmlspecialchars($cTitle); ?></h4>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest italic"><?php echo $cDate; ?></p>
                            </div>
                            <?php if ($cUri): ?>
                            <a href="<?php echo $cUri; ?>" download 
                               class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-gray-400 flex items-center justify-center hover:bg-xbox-green hover:text-white transition-all shadow-lg flex-shrink-0">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="mt-16">
                <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($cCurrentPage, $cTotalPages, $cBaseUrl, 'cpage'); ?>
            </div>

        <?php else: ?>
            <div class="py-24 text-center glass-card rounded-[3rem] border-white/5 opacity-80 relative overflow-hidden group">
                <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
                    <i class="fas fa-film text-[20rem] rotate-12 group-hover:rotate-0 transition-transform duration-700"></i>
                </div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-[2rem] bg-white/5 flex items-center justify-center text-gray-600 mb-6 border border-white/10">
                        <i class="fas fa-video text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2 italic uppercase tracking-tighter underline underline-offset-4 decoration-xbox-green">SEM REPLAY...</h3>
                    <p class="text-gray-600 font-medium max-w-sm mx-auto text-sm">Grave seus clipes épicos com o botão 'Share' do seu controle.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include('../includes/footer.php'); ?>
