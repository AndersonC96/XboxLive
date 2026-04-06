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
$user = AuthService::user();
$xuid = $user['xuid'] ?? null;

// Busca o Histórico de Títulos
$response = ($xuid) ? $api->getTitleHistory($xuid) : $api->getTitleHistory();
$allTitles = $response['titles'] ?? [];

// Lógica de Busca (Server-side)
$search = $_GET['q'] ?? '';
$filteredTitles = $allTitles;
if (!empty($search)) {
    $filteredTitles = array_filter($allTitles, function($t) use ($search) {
        return stripos($t['name'] ?? '', $search) !== false;
    });
}

// Paginação
$itemsPerPage = 10;
$totalItems = count($filteredTitles);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $itemsPerPage;
$titles = array_slice($filteredTitles, $offset, $itemsPerPage);

function getTitleArt($title) {
    if (isset($title['displayImage'])) return $title['displayImage'];
    if (isset($title['images'])) {
        foreach ($title['images'] as $img) {
            if ($img['type'] === 'BoxArt') return $img['url'];
        }
        return $title['images'][0]['url'] ?? '../img/default_game.jpg';
    }
    return '../img/default_game.jpg';
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto animate-fade-in space-y-16">
    <!-- Header Timeline -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-8 text-center md:text-left">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block text-shadow-sm">Seu Jogo</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white italic drop-shadow-lg uppercase">Linha do Tempo</h1>
            <p class="text-gray-500 font-medium max-w-xl mx-auto md:mx-0 mt-2">Uma jornada cronológica pelos mundos que você já explorou.</p>
        </div>

        <form action="" method="GET" class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar na jornada..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all"
                onchange="this.form.submit()">
        </form>
    </header>

    <?php if (!empty($titles)): ?>
        <div class="relative py-10">
            <!-- The Rail (Vertical Line) -->
            <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-xbox-green/50 via-white/10 to-transparent md:-translate-x-1/2"></div>

            <div class="space-y-20 relative">
                <?php foreach ($titles as $index => $title): ?>
                    <?php
                        $name = $title['name'] ?? 'Título Desconhecido';
                        $art = getTitleArt($title);
                        $lastPlayed = isset($title['titleHistory']['lastTimePlayed']) ? date('d M Y', strtotime($title['titleHistory']['lastTimePlayed'])) : 'Data Indisponível';
                        
                        $ach = $title['achievement'] ?? $title['achievementInfo'] ?? [];
                        $currentGs = $ach['currentGamerscore'] ?? $ach['CurrentGamerscore'] ?? 0;
                        $totalGs = $ach['totalGamerscore'] ?? $ach['TotalGamerscore'] ?? 1000;
                        $progress = $ach['progressPercentage'] ?? $ach['ProgressPercentage'] ?? 0;
                        
                        $isEven = ($index % 2 === 0);
                    ?>
                    
                    <div class="flex flex-col md:flex-row items-center gap-8 md:gap-0 w-full">
                        <!-- Node Content (Left or Right) -->
                        <div class="w-full md:w-1/2 <?php echo $isEven ? 'md:pr-16 md:text-right' : 'md:pl-16 md:order-2'; ?>">
                            <article class="glass-card group rounded-[2.5rem] p-6 hover:border-xbox-green/40 transition-all cursor-default">
                                <div class="flex <?php echo $isEven ? 'md:flex-row-reverse' : 'flex-row'; ?> gap-6 items-center">
                                    <div class="w-24 h-24 rounded-[1.5rem] overflow-hidden shadow-2xl shrink-0 border border-white/5">
                                        <img src="<?php echo htmlspecialchars($art); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-white text-lg leading-tight truncate mb-2 uppercase tracking-tight"><?php echo htmlspecialchars($name); ?></h3>
                                        <div class="flex items-center <?php echo $isEven ? 'md:justify-end' : ''; ?> gap-2 mb-3">
                                            <img src="../img/gs.png" class="w-4 h-4 opacity-80" alt="GS">
                                            <span class="text-sm font-bold text-xbox-green"><?php echo number_format($currentGs, 0, ',', '.'); ?></span>
                                            <span class="text-[10px] text-gray-500">/ <?php echo $totalGs; ?></span>
                                        </div>
                                        
                                        <!-- Mini Progress -->
                                        <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                                            <div class="h-full bg-xbox-green shadow-[0_0_8px_rgba(16,124,16,0.5)]" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- The Timeline Node (The Dot) -->
                        <div class="absolute left-4 md:left-1/2 w-8 h-8 rounded-full bg-xbox-dark border-4 border-xbox-green/30 md:-translate-x-1/2 flex items-center justify-center z-20 group-hover:border-xbox-green transition-colors">
                            <div class="w-2 h-2 rounded-full bg-xbox-green animate-pulse"></div>
                            
                            <!-- Date Badge (Floats on the opposite side of the card) -->
                            <div class="absolute left-12 md:left-auto <?php echo $isEven ? 'md:left-12' : 'md:right-12'; ?> whitespace-nowrap">
                                <span class="bg-xbox-green/10 text-xbox-green text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-lg border border-xbox-green/20">
                                    <?php echo $lastPlayed; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Empty side for balancing -->
                        <div class="w-full md:w-1/2 hidden md:block"></div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <div class="mt-24">
                <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages, 'historico.php' . ($search ? "?q=".urlencode($search) : "")); ?>
            </div>
        </div>

    <?php else: ?>
        <div class="py-32 text-center glass-card rounded-[3rem] border-white/5 opacity-80 relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
                <i class="fas fa-route text-[20rem] rotate-12"></i>
            </div>
            <div class="relative z-10">
                <div class="w-20 h-20 rounded-[2rem] bg-white/5 flex items-center justify-center text-gray-600 mx-auto mb-6 border border-white/10 shadow-inner">
                    <i class="fas fa-map-marked-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2 italic">A JORNADA ESTÁ PELA METADE...</h3>
                <p class="text-gray-600 font-medium max-w-xs mx-auto text-sm leading-relaxed">
                    <?php echo !empty($search) ? 'Nenhum jogo encontrado na sua história com esse nome.' : 'Comece a jogar para construir sua Linha do Tempo épica aqui.'; ?>
                </p>
                <?php if ($search): ?>
                    <a href="historico.php" class="inline-block mt-8 text-xbox-green font-bold text-xs uppercase tracking-widest hover:underline">Ver Jornada Completa</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>
>