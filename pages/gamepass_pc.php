<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;
use Anderson\XboxLive\Core\Database;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance();
$stmt = $db->prepare("SELECT game_id FROM pc_gamepass");
$stmt->execute();
$game_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($game_ids)) {
    $game_ids = [];
}

$items_per_page = 15;
$total_items = count($game_ids);
$total_pages = ceil($total_items / $items_per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;
$current_page_ids = array_slice($game_ids, $offset, $items_per_page);

$products = [];
if (!empty($current_page_ids)) {
    $api = new OpenXBLService();
    $response = $api->getMarketplaceDetails($current_page_ids);
    $products = $response['Products'] ?? [];
}

$baseUrl = 'gamepass_pc.php';

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Plataformas</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white uppercase">PC Game Pass</h1>
        </div>
        
        <div class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" id="gamesSearch" placeholder="Buscar no catálogo PC..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all">
        </div>
    </header>

    <?php if (!empty($products)) : ?>
        <div id="gamesList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <?php foreach ($products as $product) : ?>
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
                $productId = $product['ProductId'] ?? '';
                ?>
                </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($page, $total_pages, $baseUrl); ?>
    <?php else : ?>
        <div class="py-24 text-center glass-card rounded-3xl border-dashed">
            <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">Nenhum título PC Game Pass encontrado</p>
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