<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

$db = \Anderson\XboxLive\Core\Database::getInstance();
$stmt = $db->prepare("SELECT game_id FROM coming_soon");
$stmt->execute();
$game_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Search Logic (Server-side)
$search = $_GET['q'] ?? '';
$filtered_ids = $game_ids;

$items_per_page = 15;
$total_items = count($game_ids);
$total_pages = ceil($total_items / $items_per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $items_per_page;
$current_page_ids = array_slice($game_ids, $offset, $items_per_page);

$products = [];
if (!empty($current_page_ids)) {
    $api = new \Anderson\XboxLive\Services\OpenXBLService();
    $response = $api->getMarketplaceDetails($current_page_ids);
    $products = $response['Products'] ?? [];
}

$baseUrl = 'em_breve.php';
if (!empty($search)) {
    $baseUrl .= '?q=' . urlencode($search);
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block">Futuro</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white italic">EM BREVE</h1>
        </div>
        
        <form action="" method="GET" class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar no catálogo..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all"
                onchange="this.form.submit()">
        </form>
    </header>

    <?php if (!empty($products)) : ?>
        <div id="gamesList" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php foreach ($products as $product) : ?>
                <?php
                $props = $product['LocalizedProperties'][0] ?? [];
                $images = $props['Images'] ?? [];
                $boxArt = null;
                foreach ($images as $img) {
                    if ($img['ImagePurpose'] === 'BoxArt') {
                        $boxArt = $img['Uri'];
                        break;
                    }
                }
                $title = $props['ProductTitle'] ?? 'Sem Título';
                $dev = $props['DeveloperName'] ?? 'Coming Soon';
                $productId = $product['ProductId'] ?? '';
                $releaseDateRaw = $product['MarketProperties'][0]['OriginalReleaseDate'] ?? null;
                $releaseDate = $releaseDateRaw ? date('d/m/Y', strtotime($releaseDateRaw)) : 'Em breve';
                ?>
                <article class="glass-card group rounded-2xl overflow-hidden hover:border-xbox-green/50 transition-all game-card">
                    <a href="jogo.php?id=<?php echo htmlspecialchars($productId); ?>" class="block">
                        <div class="aspect-[2/3] relative overflow-hidden bg-xbox-surface">
                            <img src="<?php echo $boxArt ?: '../img/placeholder.png'; ?>" alt="<?php echo htmlspecialchars($title); ?>" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                            
                            <!-- Badges Overlay -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                                <span class="bg-xbox-green text-white text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest shadow-lg italic">Próximo</span>
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-transparent to-transparent opacity-80"></div>
                            
                            <div class="absolute bottom-4 left-4 right-4 translate-y-2 group-hover:translate-y-0 transition-transform">
                                <h3 class="font-bold text-white text-[13px] leading-tight mb-1 truncate"><?php echo htmlspecialchars($title); ?></h3>
                                <p class="text-[9px] font-black uppercase tracking-widest text-xbox-green truncate"><?php echo $releaseDate; ?></p>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-16">
            <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($page, $total_pages, $baseUrl); ?>
        </div>

    <?php else : ?>
        <!-- Premium Empty State -->
        <div class="py-32 text-center glass-card rounded-[3rem] border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 pointer-events-none">
                <i class="fas fa-clock text-[20rem] -top-10 -right-10 absolute rotate-12"></i>
            </div>
            
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-24 h-24 rounded-3xl bg-xbox-green/10 flex items-center justify-center text-xbox-green mb-8 shadow-inner">
                    <i class="fas fa-calendar-alt text-4xl animate-pulse"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-3 italic tracking-tight underline decoration-xbox-green decoration-4 underline-offset-8">SILÊNCIO POR ENQUANTO...</h3>
                <p class="text-gray-500 font-medium max-w-sm mx-auto leading-relaxed">
                    Estamos preparando o calendário de lançamentos mais incrível da temporada. Volte em breve para descobrir novos mundos!
                </p>
                
                <div class="mt-12 flex gap-4">
                    <a href="dashboard.php" class="px-8 py-3 rounded-2xl bg-white/5 border border-white/10 text-white font-black uppercase tracking-widest text-[10px] hover:bg-xbox-green hover:text-white transition-all shadow-xl">
                        Voltar ao Início
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    document.querySelector('input[name="q"]').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.game-card').forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            card.style.display = title.includes(term) ? '' : 'none';
        });
    });
</script>
<?php include('../includes/footer.php'); ?>
