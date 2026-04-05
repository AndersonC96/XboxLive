<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$productId = $_GET['id'] ?? null;
if (!$productId) {
    header('Location: dashboard.php');
    exit();
}

$api = new OpenXBLService();
$response = $api->getMarketplaceDetails($productId);
$product = $response['Products'][0] ?? null;

if (!$product) {
    die("Título não encontrado.");
}

// Map settings
$title = $product['LocalizedProperties'][0]['ProductTitle'] ?? 'Desconhecido';
$description = $product['LocalizedProperties'][0]['ProductDescription'] ?? 'Sem descrição disponível.';
$publisher = $product['LocalizedProperties'][0]['PublisherName'] ?? 'Desconhecido';
$developer = $product['LocalizedProperties'][0]['DeveloperName'] ?? 'Desconhecido';
$price = $product['DisplaySkuAvailabilities'][0]['Availabilities'][0]['OrderManagementData']['Price']['ListPrice'] ?? 'Grátis';
$currency = $product['DisplaySkuAvailabilities'][0]['Availabilities'][0]['OrderManagementData']['Price']['CurrencyCode'] ?? '';

// Images
$boxArt = '../img/default_game.jpg';
$heroImage = '';
foreach ($product['LocalizedProperties'][0]['Images'] as $img) {
    if ($img['ImagePurpose'] === 'BoxArt') $boxArt = $img['Uri'];
    if ($img['ImagePurpose'] === 'BrandedKeyArt' || $img['ImagePurpose'] === 'SuperHeroArt') $heroImage = $img['Uri'];
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="animate-fade-in">
    <!-- Hero Header -->
    <div class="relative h-[50vh] min-h-[400px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 scale-105" 
             style="background-image: url('<?php echo $heroImage ?: $boxArt; ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-xbox-dark/80 to-transparent"></div>
        
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-16">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-end gap-8">
                <div class="w-48 h-48 rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] border-4 border-white/10 hidden md:block">
                    <img src="<?php echo $boxArt; ?>" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Detalhes do Jogo</span>
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white mb-4"><?php echo htmlspecialchars($title); ?></h1>
                    <div class="flex flex-wrap items-center gap-6 text-sm font-bold text-gray-400">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-building text-xbox-green/50"></i> <?php echo htmlspecialchars($publisher); ?>
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-code text-xbox-green/50"></i> <?php echo htmlspecialchars($developer); ?>
                        </span>
                        <span class="px-3 py-1 rounded-full bg-xbox-green text-white text-[10px] uppercase tracking-widest">
                            <?php echo is_numeric($price) ? $currency . ' ' . number_format($price, 2, ',', '.') : $price; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-8 md:px-16 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Overview -->
        <div class="lg:col-span-2 space-y-12">
            <section>
                <h3 class="text-2xl font-black mb-6 flex items-center gap-3">
                    <i class="fas fa-align-left text-xbox-green"></i> Sobre o Título
                </h3>
                <div class="glass-card rounded-3xl p-8 border-white/5">
                    <p class="text-gray-400 leading-relaxed whitespace-pre-line">
                        <?php echo htmlspecialchars($description); ?>
                    </p>
                </div>
            </section>

            <!-- Metadata Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="glass-card rounded-2xl p-6 border-white/5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-xbox-green/10 flex items-center justify-center text-xbox-green">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Lançamento</p>
                        <p class="text-sm font-bold text-white">Consulte a Store</p>
                    </div>
                </div>
                <div class="glass-card rounded-2xl p-6 border-white/5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-xbox-green/10 flex items-center justify-center text-xbox-green">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Categorias</p>
                        <p class="text-sm font-bold text-white"><?php echo htmlspecialchars($product['Properties']['Category'] ?? 'Digital'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card rounded-3xl p-8 border-white/5 sticky top-8">
                <h4 class="text-lg font-bold mb-6">Ações Rápidas</h4>
                <div class="space-y-4">
                    <a href="https://www.xbox.com/pt-br/games/store/product/<?php echo $productId; ?>" target="_blank" 
                       class="w-full py-4 rounded-2xl bg-xbox-green text-white font-black uppercase tracking-widest text-xs text-center block hover:bg-xbox-green/80 transition-all shadow-lg shadow-xbox-green/20">
                        Ver na Microsoft Store
                    </a>
                    <button class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-white font-bold hover:bg-white/10 transition-all flex items-center justify-center gap-3 cursor-not-allowed opacity-50">
                        <i class="fas fa-plus"></i> Desejo Comprar
                    </button>
                    <div class="pt-6 border-t border-white/5">
                        <p class="text-xs text-center text-gray-600 font-medium">
                            <i class="fas fa-info-circle mr-1"></i> Preços sujeitos a alteração pela Microsoft.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
