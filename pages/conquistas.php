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
$response = $api->get("achievements");
$games = $response['titles'] ?? [];

include('../includes/header.php');
include('../includes/navbar.php');

function getBoxArtUrl($game) {
    if (isset($game['images'])) {
        foreach ($game['images'] as $image) {
            if ($image['type'] === 'BoxArt') return $image['url'];
        }
    }
    return '../img/default_game.jpg';
}
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Seu Progresso</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4">Conquistas</h1>
            <p class="text-gray-500 font-medium">Acompanhe sua jornada em todos os títulos do ecossistema Xbox.</p>
        </div>

        <div class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" id="achievementSearch" placeholder="Buscar por jogo..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all">
        </div>
    </header>

    <?php if (!empty($games)) : ?>
        <div id="achievementsList" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($games as $game) : ?>
                <?php
                    $name = $game['name'] ?? 'Título Desconhecido';
                    $currentGs = $game['achievement']['currentGamerscore'] ?? 0;
                    $totalGs = $game['achievement']['totalGamerscore'] ?? 0;
                    $progress = $game['achievement']['progressPercentage'] ?? 0;
                    $boxArt = getBoxArtUrl($game);
                ?>
                <article class="glass-card group rounded-3xl p-6 hover:border-xbox-green/40 transition-all achievement-card" data-name="<?php echo htmlspecialchars(strtolower($name)); ?>">
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
                </article>
            <?php endforeach; ?>
        </div>

        <div id="paginationContainer" class="mt-16 flex items-center justify-center gap-2"></div>
    <?php else : ?>
        <div class="py-32 text-center glass-card rounded-3xl border-dashed">
            <i class="fas fa-trophy text-6xl text-gray-800 mb-6"></i>
            <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">Nenhuma conquista registrada</p>
        </div>
    <?php endif; ?>
</main>

<script>
    const searchInput = document.getElementById('achievementSearch');
    const achievementsList = document.getElementById('achievementsList');
    const paginationContainer = document.getElementById('paginationContainer');
    const cards = Array.from(document.querySelectorAll('.achievement-card'));
    const itemsPerPage = 12;
    let currentPage = 1;

    function render() {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredCards = cards.filter(card => card.getAttribute('data-name').includes(searchTerm));
        const totalPages = Math.ceil(filteredCards.length / itemsPerPage);

        // Hide all
        cards.forEach(card => card.style.display = 'none');

        // Show current page
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = filteredCards.slice(start, end);
        pageItems.forEach(card => card.style.display = 'block');

    function renderPagination(totalPages) {
        paginationContainer.innerHTML = '';
        if (totalPages <= 1) return;

        // Helper: Create button
        const createBtn = (content, page, isActive = false, isDisabled = false) => {
            const btn = document.createElement('button');
            btn.innerHTML = content;
            btn.disabled = isDisabled;
            btn.className = `w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all ${
                isActive 
                ? 'bg-xbox-green text-white shadow-[0_0_15px_rgba(16,124,16,0.5)] z-10' 
                : isDisabled 
                    ? 'bg-white/5 text-gray-800 cursor-not-allowed' 
                    : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white'
            }`;
            if (!isDisabled) {
                btn.onclick = () => {
                    currentPage = page;
                    render();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                };
            }
            return btn;
        };

        // Previous
        paginationContainer.appendChild(createBtn('<i class="fas fa-chevron-left"></i>', currentPage - 1, false, currentPage === 1));

        // Smart Numbers Logic
        const range = 1; // Numbers around current
        const showEllipsesAt = 3;

        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || // Always first
                i === totalPages || // Always last
                (i >= currentPage - range && i <= currentPage + range) // Range around current
            ) {
                paginationContainer.appendChild(createBtn(i, i, i === currentPage));
            } else if (
                (i === currentPage - range - 1 && i > 1) || 
                (i === currentPage + range + 1 && i < totalPages)
            ) {
                const dot = document.createElement('span');
                dot.textContent = '...';
                dot.className = 'w-8 text-center text-gray-700 font-black';
                paginationContainer.appendChild(dot);
            }
        }

        // Next
        paginationContainer.appendChild(createBtn('<i class="fas fa-chevron-right"></i>', currentPage + 1, false, currentPage === totalPages));
    }

    function render() {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredCards = cards.filter(card => card.getAttribute('data-name').includes(searchTerm));
        const totalPages = Math.ceil(filteredCards.length / itemsPerPage);

        // UI Feedback: Zero results
        if (filteredCards.length === 0) {
            achievementsList.innerHTML = `
                <div class="col-span-full py-32 text-center glass-card rounded-3xl border-dashed animate-fade-in">
                    <i class="fas fa-ghost text-6xl text-gray-800 mb-6"></i>
                    <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">Título não encontrado</p>
                </div>
            `;
        } else {
            // Restore original container if it was emptied for ghost state
            // In our case, cards are just hidden, so we just manage visibility
            achievementsList.querySelectorAll('.zero-state').forEach(el => el.remove());
        }

        // Hide all
        cards.forEach(card => card.style.display = 'none');

        // Show current page
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = filteredCards.slice(start, end);
        pageItems.forEach(card => card.style.display = 'block');

        // Render Pagination
        renderPagination(totalPages);
    }

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        render();
    });

    // Initial render
    render();
</script>

<?php include('../includes/footer.php'); ?>