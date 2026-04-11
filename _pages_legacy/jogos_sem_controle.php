<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

if (!isset($_SESSION['user_id'])) {
    echo "Erro: Usuário não está logado.";
    exit;
}

$db = \Anderson\XboxLive\Core\Database::getInstance();
$stmt = $db->prepare("SELECT game_id FROM cloud_gamepass");
$stmt->execute();
$game_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Search Logic (Server-side)
$search = $_GET['q'] ?? '';
$filtered_ids = $game_ids;

if (!empty($search)) {
    // Note: Since we only have IDs in this table, real search for names 
    // would ideally be done in a unified games table. 
    // For now, we keep the UI-side search or use a simple hack.
}

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

$baseUrl = 'jogos_sem_controle.php';
if (!empty($search)) {
    $baseUrl .= '?q=' . urlencode($search);
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block">Cloud Gaming</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white italic">CONTROLES POR TOQUE</h1>
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
                $dev = $props['DeveloperName'] ?? 'Xbox Cloud';
                $productId = $product['ProductId'] ?? '';
                ?>
                <article class="glass-card group rounded-2xl overflow-hidden hover:border-xbox-green/50 transition-all game-card">
                    <a href="jogo.php?id=<?php echo htmlspecialchars($productId); ?>" class="block">
                        <div class="aspect-[2/3] relative overflow-hidden bg-xbox-surface">
                            <img src="<?php echo $boxArt ?: '../img/placeholder.png'; ?>" alt="<?php echo htmlspecialchars($title); ?>" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                            
                            <!-- Badges Overlay -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                                <span class="bg-xbox-green text-white text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest shadow-lg">Cloud</span>
                                <span class="bg-black/60 backdrop-blur-md text-white text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest">Touch</span>
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-transparent to-transparent opacity-80"></div>
                            
                            <div class="absolute bottom-4 left-4 right-4 translate-y-2 group-hover:translate-y-0 transition-transform">
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
        <div class="py-24 text-center glass-card rounded-3xl border-dashed">
            <i class="fas fa-fingerprint text-6xl text-gray-800 mb-6"></i>
            <p class="text-base font-bold text-gray-600 uppercase tracking-widest">Nenhum jogo por toque encontrado</p>
            <p class="text-xs text-gray-700 mt-2">O catálogo da nuvem está sendo atualizado no momento.</p>
        </div>
    <?php endif; ?>
</main>

<script>
    // Local filtering for speed, while maintaining server search for accuracy
    document.querySelector('input[name="q"]').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.game-card').forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            card.style.display = title.includes(term) ? '' : 'none';
        });
    });
</script>
<?php include('../includes/footer.php'); ?>