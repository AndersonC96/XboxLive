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

$response = null;
if ($xuid) {
    // Busca o Histórico de Títulos (Mais robusto que apenas conquistas isoladas)
    $response = $api->getTitleHistory($xuid);
}

// Fallback para histórico global se XUID não existir
if (!$response) {
    $response = $api->getTitleHistory();
}

$allGames = $response['titles'] ?? [];

// Search Logic (Server-side)
$search = $_GET['q'] ?? '';
$filteredGames = $allGames;
if (!empty($search)) {
    $filteredGames = array_filter($allGames, function($game) use ($search) {
        $gameName = $game['name'] ?? '';
        return stripos($gameName, $search) !== false;
    });
}

// Pagination Logic (Server-side)
$itemsPerPage = 12;
$totalItems = count($filteredGames);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

$offset = ($currentPage - 1) * $itemsPerPage;
$games = array_slice($filteredGames, $offset, $itemsPerPage);

$baseUrl = 'conquistas.php';
if (!empty($search)) {
    $baseUrl .= '?q=' . urlencode($search);
}

include('../includes/header.php');
include('../includes/navbar.php');

function getBoxArtUrl($game) {
    // Tenta primeiro a imagem de exibição direta (comum no Title History)
    if (isset($game['displayImage'])) return $game['displayImage'];

    if (isset($game['images'])) {
        foreach ($game['images'] as $image) {
            // Prioridade para BoxArt
            if ($image['type'] === 'BoxArt') return $image['url'];
        }
        // Fallback para qualquer imagem se não houver BoxArt
        if (!empty($game['images'])) return $game['images'][0]['url'];
    }
    return '../img/default_game.jpg';
}
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block text-shadow-sm">Seu Progresso</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4 italic drop-shadow-lg uppercase">Meus Jogos</h1>
            <p class="text-gray-500 font-medium">Acompanhe sua jornada e conquistas em todos os títulos que você já jogou.</p>
        </div>

        <form action="" method="GET" class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar por jogo..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all"
                onchange="this.form.submit()">
        </form>
    </header>

    <?php if (!empty($games)) : ?>
        <div id="achievementsList" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($games as $game) : ?>
                <?php
                    $name = $game['name'] ?? 'Título Desconhecido';
                    
                    // Mapeamento Flexível de Conquistas (Suporta diferentes versões da API)
                    $ach = $game['achievement'] ?? $game['achievementInfo'] ?? [];
                    $currentGs = $ach['currentGamerscore'] ?? $ach['CurrentGamerscore'] ?? 0;
                    $totalGs = $ach['totalGamerscore'] ?? $ach['TotalGamerscore'] ?? 1000;
                    $progress = $ach['progressPercentage'] ?? $ach['ProgressPercentage'] ?? 0;
                    
                    // Se o progresso for 0 mas houver GS, calcula manualmente para garantir exibição
                    if ($progress == 0 && $totalGs > 0 && $currentGs > 0) {
                        $progress = round(($currentGs / $totalGs) * 100);
                    }

                    // Prioriza o ProductId (GUID) para a Loja, fallback para TitleId (numérico)
                    $gameId = $game['productId'] ?? ($game['titleId'] ?? '');
                    $boxArt = getBoxArtUrl($game);
                ?>
                <a href="jogo.php?id=<?php echo $gameId; ?>" class="glass-card group rounded-3xl p-6 hover:border-xbox-green/40 transition-all block">
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
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages, $baseUrl); ?>
        
    <?php else : ?>
        <div class="py-32 text-center glass-card rounded-3xl border-dashed">
            <i class="fas fa-search-plus text-6xl text-gray-800 mb-6"></i>
            <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">
                <?php echo !empty($search) ? 'Nenhum resultado para "' . htmlspecialchars($search) . '"' : 'Nenhuma conquista registrada'; ?>
            </p>
            <?php if (!empty($search)) : ?>
                <a href="conquistas.php" class="inline-block mt-6 text-xbox-green font-bold hover:underline">Limpar busca</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>