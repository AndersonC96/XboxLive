<?php
session_start();
include('../includes/header.php');
include('../includes/navbar.php');
require '../config/db.php';
require_once '../config/api.php';

if (!isset($_SESSION['user_id'])) {
    echo "Erro: Usuário não está logado.";
    exit;
}

$endpoint = "recent-players";
$response = openXBLRequest($endpoint);

if ($response && isset($response['people']) && is_array($response['people'])) {
    $recentPlayers = $response['people'];
} else {
    $recentPlayers = [];
}
?>
<main class="xbox-content">
    <div class="xbox-page space-y-6">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Comunidade</span>
            <h1 class="xbox-hero-title">Jogadores Recentes</h1>
        </section>

        <div class="xbox-panel space-y-4">
            <div class="friends-search">
                <i class="fas fa-search text-green-200/80"></i>
                <input
                    type="text"
                    id="filterRecentGamertag"
                    placeholder="Buscar por Gamertag..."
                    class="friends-search-input" />
                <button id="recentSearchButton" class="friends-search-btn" aria-label="Buscar">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($recentPlayers)) : ?>
            <div id="recentList" class="friend-grid">
                <?php foreach ($recentPlayers as $player) : ?>
                    <?php
                    $avatar = !empty($player['displayPicRaw']) ? $player['displayPicRaw'] : '../img/default_avatar.jpg';
                    $gamertag = $player['gamertag'] ?? ($player['displayName'] ?? 'Usuário');
                    $recentTitle = $player['recentPlayer']['titles'][0]['titleName'] ?? 'Jogo recente não informado';
                    $lastPlayedRaw = $player['recentPlayer']['titles'][0]['lastPlayedWithDateTime'] ?? null;
                    $lastPlayed = $lastPlayedRaw ? date('d/m/Y', strtotime($lastPlayedRaw)) : 'Data não disponível';
                    $gamerscore = isset($player['gamerScore']) ? number_format($player['gamerScore'], 0, ',', '.') : '0';
                    ?>
                    <article
                        class="friend-card xbox-glass-card"
                        data-gamertag="<?php echo htmlspecialchars($gamertag); ?>">
                        <div class="friend-card-header">
                            <span class="friend-status-dot"></span>
                            <span class="friend-added">Jogou recentemente</span>
                        </div>
                        <div class="friend-card-body">
                            <div class="friend-avatar">
                                <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo htmlspecialchars($gamertag); ?>" />
                            </div>
                            <div class="friend-details">
                                <div class="friend-gamertag"><?php echo htmlspecialchars($gamertag); ?></div>
                                <div class="friend-presence">Último jogo: <?php echo htmlspecialchars($recentTitle); ?></div>
                                <div class="friend-presence">Último encontro: <?php echo htmlspecialchars($lastPlayed); ?></div>
                                <div class="friend-gamerscore">
                                    <strong><?php echo $gamerscore; ?></strong>
                                    <img src="../img/gs.png" alt="Gamerscore Icon" class="friend-gs-icon" />
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div id="recentPagination" class="friends-pagination"></div>
        <?php else : ?>
            <p class="text-green-50">Você não jogou com outros jogadores recentemente.</p>
        <?php endif; ?>
    </div>
</main>
<script>
    const recentSearchInput = document.getElementById('filterRecentGamertag');
    const recentSearchButton = document.getElementById('recentSearchButton');
    const recentList = document.getElementById('recentList');
    const recentPagination = document.getElementById('recentPagination');
    const recentCards = recentList ? Array.from(recentList.querySelectorAll('.friend-card')) : [];
    const RECENT_PAGE_SIZE = 12;
    let recentCurrentPage = 1;

    function applyRecentFilters() {
        const gamertagTerm = recentSearchInput.value.toLowerCase();
        return recentCards.filter((card) => {
            const gamertag = card.getAttribute('data-gamertag').toLowerCase();
            return gamertag.includes(gamertagTerm);
        });
    }

    function renderRecentPagination(totalPages) {
        recentPagination.innerHTML = '';
        if (totalPages <= 1) {
            return;
        }

        const createButton = (label, page, {
            isActive = false,
            disabled = false
        } = {}) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.className = `pagination-btn ${isActive ? 'active' : ''} ${disabled ? 'disabled' : ''}`;
            if (!disabled) {
                button.addEventListener('click', () => {
                    recentCurrentPage = page;
                    updateRecent();
                });
            }
            return button;
        };

        const addSeparator = () => {
            const separator = document.createElement('span');
            separator.className = 'pagination-separator';
            separator.textContent = '';
            recentPagination.appendChild(separator);
        };

        const maxVisible = 5;
        const halfWindow = Math.floor(maxVisible / 2);
        let startPage = Math.max(1, recentCurrentPage - halfWindow);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        const hasPrev = recentCurrentPage > 1;
        const hasNext = recentCurrentPage < totalPages;

        recentPagination.appendChild(
            createButton('Anterior', recentCurrentPage - 1, {
                disabled: !hasPrev
            })
        );

        addSeparator();

        for (let page = startPage; page <= endPage; page++) {
            recentPagination.appendChild(createButton(page, page, {
                isActive: page === recentCurrentPage
            }));
            if (page < endPage) addSeparator();
        }

        addSeparator();

        recentPagination.appendChild(
            createButton('Próximo', recentCurrentPage + 1, {
                disabled: !hasNext
            })
        );
    }

    function updateRecent() {
        if (!recentList) return;

        const filtered = applyRecentFilters();
        const totalPages = Math.max(1, Math.ceil(filtered.length / RECENT_PAGE_SIZE));
        recentCurrentPage = Math.min(recentCurrentPage, totalPages);

        recentCards.forEach((card) => {
            card.style.display = 'none';
        });

        const start = (recentCurrentPage - 1) * RECENT_PAGE_SIZE;
        const visibleCards = filtered.slice(start, start + RECENT_PAGE_SIZE);
        visibleCards.forEach((card) => {
            card.style.display = '';
        });

        renderRecentPagination(totalPages);
    }

    if (recentList) {
        recentSearchInput.addEventListener('input', () => {
            recentCurrentPage = 1;
            updateRecent();
        });

        recentSearchButton.addEventListener('click', () => {
            recentCurrentPage = 1;
            updateRecent();
        });

        updateRecent();
    }
</script>
<?php include('../includes/footer.php'); ?>