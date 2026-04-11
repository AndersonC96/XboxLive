<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

if (!\Anderson\XboxLive\Services\AuthService::check()) {
    header('Location: login.php');
    exit();
}

$api = new \Anderson\XboxLive\Services\OpenXBLService();

// Tentamos o endpoint de melhores avaliados (que pode ser instável em certas regiões)
$endpoint = "marketplace/best-rated";
$response = $api->get($endpoint);

$products = $response['Products'] ?? [];
$items = $response['Items'] ?? [];

// Configuração de Paginação
$items_per_page = 15;
$total_items = count($products);
$total_pages = ceil($total_items / $items_per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $items_per_page;
$current_page_products = array_slice($products, $offset, $items_per_page);

$baseUrl = 'melhores_avaliados.php';

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block text-shadow-sm">Ranking</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white italic drop-shadow-lg">MELHORES AVALIADOS</h1>
        </div>
        
        <div class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" id="gamesSearch" placeholder="Buscar no Top 50..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all shadow-inner">
        </div>
    </header>

    <?php if (!empty($current_page_products)) : ?>
        <div id="gamesList" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php foreach ($current_page_products as $product) : ?>
                <?php
                $props = $product['LocalizedProperties'][0] ?? [];
                $images = $props['Images'] ?? [];
                $boxArt = null;
                foreach ($images as $img) {
                    if ($img['ImagePurpose'] === 'BoxArt') {
                        $boxArt = 'https:' . $img['Uri'];
                        break;
                    }
                }
                $title = $props['ProductTitle'] ?? 'Sem Título';
                $dev = $props['DeveloperName'] ?? 'Estúdio Xbox';
                $productId = $product['ProductId'] ?? '';
                
                // Busca de score nos Items (metadados adjacentes)
                $score = '4.8'; // Fallback
                foreach ($items as $item) {
                    if (($item['Id'] ?? '') === $productId && isset($item['PredictedScore'])) {
                        $score = number_format($item['PredictedScore'], 1);
                        break;
                    }
                }
                ?>
                <article class="glass-card group rounded-2xl overflow-hidden hover:border-xbox-green/40 transition-all game-card" data-title="<?php echo htmlspecialchars(strtolower($title)); ?>">
                    <a href="jogo.php?id=<?php echo htmlspecialchars($productId); ?>" class="block">
                        <div class="aspect-[2/3] relative overflow-hidden bg-xbox-surface">
                            <img src="<?php echo $boxArt ?: '../img/placeholder.png'; ?>" alt="<?php echo htmlspecialchars($title); ?>" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                            
                            <!-- Score Badge Overlay -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                                <span class="bg-xbox-dark/80 backdrop-blur-md text-white text-[9px] font-black px-2 py-1 rounded-lg border border-white/10 flex items-center gap-1.5 shadow-2xl">
                                    <i class="fas fa-star text-xbox-green"></i> <?php echo $score; ?>
                                </span>
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark/95 via-xbox-dark/30 to-transparent"></div>
                            
                            <div class="absolute bottom-4 left-4 right-4 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                <h3 class="font-bold text-white text-[13px] leading-tight mb-1 truncate"><?php echo htmlspecialchars($title); ?></h3>
                                <p class="text-[9px] font-black uppercase tracking-widest text-xbox-green truncate"><?php echo htmlspecialchars($dev); ?></p>
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
        <!-- Premium Empty State (Trophy/Star themed) -->
        <div class="py-32 text-center glass-card rounded-[3rem] border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
                <i class="fas fa-trophy text-[30rem] rotate-12"></i>
            </div>
            
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-yellow-400/20 to-yellow-600/10 flex items-center justify-center text-yellow-500 mb-8 shadow-inner border border-yellow-500/20">
                    <i class="fas fa-star text-4xl animate-spin-slow"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-3 italic tracking-tight underline decoration-yellow-500 decoration-4 underline-offset-8">PODIUM EM MANUTENÇÃO...</h3>
                <p class="text-gray-500 font-medium max-w-sm mx-auto leading-relaxed">
                    Estamos recalculando as avaliações globais para trazer apenas o melhor do melhor. Volte em alguns minutos!
                </p>
                
                <div class="mt-12 group">
                    <a href="dashboard.php" class="px-8 py-3 rounded-2xl bg-white/5 border border-white/10 text-white font-black uppercase tracking-widest text-[10px] hover:bg-yellow-500 hover:text-xbox-dark transition-all shadow-xl flex items-center gap-3">
                        <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    document.getElementById('gamesSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.game-card').forEach(card => {
            const title = card.getAttribute('data-title');
            card.style.display = title.includes(term) ? 'block' : 'none';
        });
    });
</script>
<?php include('../includes/footer.php'); ?>
