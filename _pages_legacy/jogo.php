<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$idParam = $_GET['id'] ?? null;
if (!$idParam) {
    header('Location: dashboard.php');
    exit();
}

$api = new OpenXBLService();
$user = AuthService::user();
$productId = $idParam;
$historyTitle = null; // Guardamos os dados do histórico caso precisemos de Fallback

// --- PONTE DE INTELIGÊNCIA V3 ---
// Se o ID for numérico (TitleId), precisamos resolver para ProductId (GUID)
if (is_numeric($idParam)) {
    $history = ($user['xuid']) ? $api->getTitleHistory($user['xuid']) : $api->getTitleHistory();
    $titles = $history['titles'] ?? [];
    
    foreach ($titles as $t) {
        if (($t['titleId'] ?? '') == $idParam) {
            $historyTitle = $t;
            // PRIORIDADE 1: Se o histórico já tem o productId (GUID), usamos direto! (Corrige CoD/Modernos)
            if (!empty($t['productId'])) {
                $productId = $t['productId'];
            } 
            // PRIORIDADE 2: Se não tem GUID (Xbox 360), tentamos a busca sanitizada
            else {
                $rawName = $t['name'] ?? '';
                // Limpa o nome para busca: remove símbolos e termos de edições que confundem a Store
                $cleanName = preg_replace('/(®|™|©|Standard Edition|Legendary Edition|Game of the Year)/i', '', $rawName);
                $searchResponse = $api->get("marketplace/search?q=" . urlencode(trim($cleanName)));
                $found = $searchResponse['Products'][0]['ProductId'] ?? null;
                if ($found) $productId = $found;
            }
            break;
        }
    }
}

// Detalhes do Produto (Oficial)
$response = $api->getMarketplaceDetails($productId);
$product = $response['Products'][0] ?? null;

// Estatísticas do Jogador
$statsData = null;
if ($product) {
    $statsResponse = $api->getPlayerStats($productId, $user['xuid'] ?? null);
    $statsData = $statsResponse['groups'][0]['stats'] ?? [];
}

// --- FALLBACK: MODO LEGADO (Se não houver detalhes no Marketplace mas houver no histórico) ---
if (!$product && $historyTitle) {
    include('../includes/header.php');
    include('../includes/navbar.php');
    
    $name = $historyTitle['name'] ?? 'Título Legado';
    $ach = $historyTitle['achievement'] ?? $historyTitle['achievementInfo'] ?? [];
    $prog = $ach['progressPercentage'] ?? $ach['ProgressPercentage'] ?? 0;
    $date = isset($historyTitle['titleHistory']['lastTimePlayed']) ? date('d/m/Y', strtotime($historyTitle['titleHistory']['lastTimePlayed'])) : 'Data Indisponível';
    ?>
    <main class="animate-fade-in pb-20">
        <div class="relative h-[40vh] w-full overflow-hidden bg-xbox-dark">
             <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-transparent to-transparent"></div>
             <div class="absolute inset-0 opacity-10 flex items-center justify-center">
                 <i class="fas fa-compact-disc text-[25rem] animate-spin-slow"></i>
             </div>
             <div class="absolute bottom-0 left-0 w-full p-8 md:p-16 text-center">
                 <span class="text-[10px] font-black uppercase tracking-[0.5em] text-xbox-green mb-4 block">Relíquia Legada (Xbox 360)</span>
                 <h1 class="text-4xl md:text-7xl font-black tracking-tight text-white italic uppercase"><?php echo htmlspecialchars($name); ?></h1>
             </div>
        </div>

        <div class="max-w-4xl mx-auto px-8 py-16">
            <div class="glass-card rounded-[3rem] p-12 text-center border-white/5 relative overflow-hidden">
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-xbox-green/5 blur-3xl rounded-full"></div>
                
                <h3 class="text-2xl font-black text-white mb-8 italic uppercase tracking-tighter decoration-xbox-green decoration-4 underline underline-offset-8">HISTÓRICO PRESERVADO</h3>
                <p class="text-gray-500 font-medium max-w-lg mx-auto mb-12">
                    Este título não está mais catalogado na loja moderna, mas sua jornada e conquistas continuam salvas na nuvem.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6 rounded-3xl bg-white/5 border border-white/5">
                        <p class="text-[10px] font-black uppercase text-gray-600 mb-2 tracking-widest">Progresso</p>
                        <div class="text-3xl font-black text-xbox-green italic"><?php echo $prog; ?>%</div>
                    </div>
                    <div class="p-6 rounded-3xl bg-white/5 border border-white/5">
                        <p class="text-[10px] font-black uppercase text-gray-600 mb-2 tracking-widest">Gamerscore</p>
                        <div class="text-3xl font-black text-white italic"><?php echo number_format($ach['currentGamerscore'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="p-6 rounded-3xl bg-white/5 border border-white/5">
                        <p class="text-[10px] font-black uppercase text-gray-600 mb-2 tracking-widest">Última Vez</p>
                        <div class="text-3xl font-black text-gray-400 italic"><?php echo $date; ?></div>
                    </div>
                </div>

                <div class="mt-16">
                    <a href="historico.php" class="px-12 py-4 rounded-2xl bg-xbox-green text-white font-black uppercase tracking-widest text-[10px] hover:scale-105 transition-all shadow-2xl shadow-xbox-green/20">
                        VOLTAR À JORNADA <i class="fas fa-arrow-left ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>
    <?php
    include('../includes/footer.php');
    exit;
}

// --- ERRO TOTAL (Caso não exista nem no histórico) ---
if (!$product) {
    include('../includes/header.php');
    include('../includes/navbar.php');
    ?>
    <main class="py-32 px-4 text-center glass-card m-10 rounded-[3rem] border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent relative overflow-hidden">
        <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
            <i class="fas fa-ghost text-[30rem] rotate-12"></i>
        </div>
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-24 h-24 rounded-[2rem] bg-xbox-green/10 flex items-center justify-center text-xbox-green mb-8 shadow-inner border border-xbox-green/20">
                <i class="fas fa-ghost text-4xl"></i>
            </div>
            <h3 class="text-3xl font-black text-white mb-3 italic tracking-tight underline decoration-xbox-green decoration-4 underline-offset-8">TITULO NÃO CATALOGADO</h3>
            <p class="text-gray-500 font-medium max-w-sm mx-auto leading-relaxed mt-4">
                Este título pode ser um clássico legado ou exclusivo de outra região que não está disponível no Marketplace atual.
            </p>
            <div class="mt-12">
                <a href="conquistas.php" class="px-10 py-4 rounded-2xl bg-white/5 border border-white/10 text-white font-black uppercase tracking-widest text-[11px] hover:bg-xbox-green hover:text-white transition-all shadow-xl inline-flex items-center gap-3">
                    <i class="fas fa-arrow-left"></i> Voltar ao Meu Histórico
                </a>
            </div>
        </div>
    </main>
    <?php
    include('../includes/footer.php');
    exit;
}

// Basic Info
$props = $product['LocalizedProperties'][0] ?? [];
$title = $props['ProductTitle'] ?? 'Desconhecido';
$description = $props['ProductDescription'] ?? 'Sem descrição disponível.';
$publisher = $props['PublisherName'] ?? 'Desconhecido';
$developer = $props['DeveloperName'] ?? 'Desconhecido';

// Price Logic
$priceData = $product['DisplaySkuAvailabilities'][0]['Availabilities'][0]['OrderManagementData']['Price'] ?? [];
$price = $priceData['ListPrice'] ?? 'Grátis';
$currency = $priceData['CurrencyCode'] ?? 'USD';

// Market Stats
$marketProps = $product['MarketProperties'][0] ?? [];
$releaseDateRaw = $marketProps['OriginalReleaseDate'] ?? null;
$releaseDate = $releaseDateRaw ? date('d/m/Y', strtotime($releaseDateRaw)) : 'Data indisponível';
$category = $product['Properties']['Category'] ?? 'Digital';
$rating = $marketProps['Ratings'][0]['RatingId'] ?? 'PEGI 3';

// Images & Gallery
$boxArt = '../img/default_game.jpg';
$heroImage = '';
$screenshots = [];
$images = $props['Images'] ?? [];
foreach ($images as $img) {
    if ($img['ImagePurpose'] === 'BoxArt') $boxArt = $img['Uri'];
    if (in_array($img['ImagePurpose'], ['BrandedKeyArt', 'SuperHeroArt', 'Poster'])) $heroImage = $img['Uri'];
    if ($img['ImagePurpose'] === 'Screenshot') $screenshots[] = $img['Uri'];
}

// Capabilities (Attributes)
$attributes = $product['Properties']['Attributes'] ?? [];
$capabilities = [];
foreach ($attributes as $attr) {
    if (isset($attr['Name'])) $capabilities[] = $attr['Name'];
}

function getStatIcon($name) {
    $n = strtolower($name);
    if (stripos($n, 'time') !== false || stripos($n, 'played') !== false) return 'fas fa-clock';
    if (stripos($n, 'kill') !== false || stripos($n, 'enemy') !== false) return 'fas fa-crosshairs';
    if (stripos($n, 'death') !== false) return 'fas fa-skull';
    if (stripos($n, 'score') !== false || stripos($n, 'point') !== false) return 'fas fa-star';
    if (stripos($n, 'won') !== false || stripos($n, 'win') !== false) return 'fas fa-trophy';
    if (stripos($n, 'distance') !== false || stripos($n, 'miles') !== false) return 'fas fa-road';
    return 'fas fa-chart-line';
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="animate-fade-in pb-20">
    <!-- Hero Header -->
    <div class="relative h-[60vh] min-h-[500px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 scale-105" 
             style="background-image: url('<?php echo $heroImage ?: $boxArt; ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-xbox-dark/80 to-transparent"></div>
        
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-16">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-end gap-10 text-center md:text-left">
                <div class="w-56 h-56 rounded-3xl overflow-hidden shadow-[0_30px_60px_rgba(0,0,0,0.8)] border-4 border-white/10 hidden md:block group">
                    <img src="<?php echo $boxArt; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green">Marketplace Oficial</span>
                        <span class="h-1 w-1 bg-gray-500 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest"><?php echo htmlspecialchars($category); ?></span>
                    </div>
                    <h1 class="text-4xl md:text-7xl font-black tracking-tight text-white mb-6 leading-none italic uppercase"><?php echo htmlspecialchars($title); ?></h1>
                    
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-8 text-sm font-bold text-gray-400">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-building text-xbox-green/50"></i> <?php echo htmlspecialchars($publisher); ?>
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-code text-xbox-green/50"></i> <?php echo htmlspecialchars($developer); ?>
                        </span>
                        <span class="px-4 py-1.5 rounded-full bg-xbox-green text-white text-xs uppercase tracking-widest font-black shadow-lg shadow-xbox-green/30">
                            <?php echo is_numeric($price) ? $currency . ' ' . number_format($price, 2, ',', '.') : $price; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-8 md:px-16 py-12 grid grid-cols-1 lg:grid-cols-3 gap-16">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-16">
            
            <!-- Statistics Section (New) -->
            <?php if (!empty($statsData)) : ?>
            <section>
                <h3 class="text-2xl font-black mb-8 flex items-center gap-4">
                    <i class="fas fa-chart-bar text-xbox-green"></i> Sua Performance
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($statsData as $s) : ?>
                        <?php 
                            $sName = $s['name'] ?? 'Stat';
                            $sValue = $s['value'] ?? '0';
                            if (is_numeric($sValue) && $sValue > 1000) $sValue = number_format($sValue, 0, ',', '.');
                        ?>
                        <div class="glass-card rounded-2xl p-6 border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent hover:border-xbox-green/40 transition-all group overflow-hidden relative">
                            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 group-hover:scale-110 transition-all duration-700">
                                <i class="<?php echo getStatIcon($sName); ?> text-6xl text-white"></i>
                            </div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xbox-green shadow-inner">
                                    <i class="<?php echo getStatIcon($sName); ?>"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest"><?php echo str_replace('_', ' ', $sName); ?></span>
                            </div>
                            <div class="text-3xl font-black text-white italic tracking-tight uppercase"><?php echo htmlspecialchars($sValue); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Description -->
            <section>
                <h3 class="text-2xl font-black mb-8 flex items-center gap-4">
                    <i class="fas fa-file-alt text-xbox-green"></i> Visão Geral
                </h3>
                <div class="glass-card rounded-3xl p-10 border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <i class="fas fa-quote-right text-8xl text-white"></i>
                    </div>
                    <p class="text-gray-300 leading-relaxed font-medium text-lg relative z-10 whitespace-pre-line opacity-90">
                        <?php echo htmlspecialchars($description); ?>
                    </p>
                </div>
            </section>

            <!-- Technical Capabilities -->
            <?php if (!empty($capabilities)) : ?>
            <section>
                <h3 class="text-2xl font-black mb-8 flex items-center gap-4">
                    <i class="fas fa-shield-halved text-xbox-green"></i> Recursos do Título
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <?php foreach ($capabilities as $cap) : ?>
                        <div class="glass-card rounded-2xl p-4 border-white/5 hover:border-xbox-green/30 transition-all flex flex-col items-center justify-center text-center gap-3 group">
                            <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 group-hover:text-xbox-green transition-colors">
                                <i class="fas fa-circle-check text-xs"></i>
                            </div>
                            <span class="text-[8px] font-black uppercase tracking-widest text-gray-500 group-hover:text-white"><?php echo str_replace('_', ' ', $cap); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Screenshot Gallery -->
            <?php if (!empty($screenshots)) : ?>
            <section>
                <h3 class="text-2xl font-black mb-8 flex items-center gap-4">
                    <i class="fas fa-camera-retro text-xbox-green"></i> Galeria Oficial
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php foreach ($screenshots as $index => $ss) : ?>
                        <div class="glass-card rounded-2xl overflow-hidden border-white/5 group relative cursor-pointer <?php echo ($index == 0) ? 'sm:col-span-2' : ''; ?>">
                            <div class="aspect-video relative overflow-hidden">
                                <img src="<?php echo $ss; ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <i class="fas fa-expand text-white text-3xl"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-8">
            <div class="glass-card rounded-[2.5rem] p-10 border-white/5 sticky top-8 space-y-10 shadow-2xl">
                <h4 class="text-xl font-black italic uppercase tracking-tighter decoration-xbox-green decoration-2 underline">Informações</h4>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-xbox-green/10 flex items-center justify-center text-xbox-green">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-0.5">Lançamento</p>
                            <p class="text-sm font-bold text-white"><?php echo $releaseDate; ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-xbox-green/10 flex items-center justify-center text-xbox-green text-lg font-black">
                            <span class="text-sm"><?php echo preg_replace('/[^0-9]/', '', $rating) ?: '+3'; ?></span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-0.5">Classificação</p>
                            <p class="text-sm font-bold text-white"><?php echo $rating; ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-xbox-green/10 flex items-center justify-center text-xbox-green">
                            <i class="fas fa-download"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-0.5">Tamanho</p>
                            <p class="text-sm font-bold text-white">Consulte a Store</p>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-white/5 space-y-4">
                    <a href="https://www.xbox.com/pt-br/games/store/product/<?php echo $productId; ?>" target="_blank" 
                       class="w-full py-4 rounded-2xl bg-xbox-green text-white font-black uppercase tracking-[0.2em] text-[10px] text-center block hover:scale-[1.02] hover:shadow-[0_15px_40px_rgba(16,124,16,0.4)] transition-all">
                        COMPRAR NA STORE <i class="fas fa-external-link-alt ml-2"></i>
                    </a>
                    <button class="w-full py-4 rounded-2xl border border-white/10 text-gray-500 font-black uppercase tracking-[0.2em] text-[10px] hover:bg-white/5 transition-all">
                        ADICIONAR À LISTA
                    </button>
                    <p class="text-[9px] text-center text-gray-600 font-bold uppercase tracking-widest pt-4">
                        Preços sujeitos a alteração.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
