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

$endpoint = "achievements";
$response = openXBLRequest($endpoint);

if ($response && isset($response['titles']) && is_array($response['titles'])) {
    $games = $response['titles'];
} else {
    $games = [];
    $error_message = "Não foi possível carregar suas conquistas. Verifique se a chave da API está configurada corretamente no arquivo .env";
}

$items_per_page = 12;

function getDeviceIcon($deviceType)
{
    switch ($deviceType) {
        case 'XboxSeries':
            return '<img src="../img/xboxseries.png" alt="Xbox Series" width="24" height="24">';
        case 'PC':
        case 'Win32':
            return '<img src="../img/windows.png" alt="PC" width="24" height="24">';
        case 'XboxOne':
            return '<img src="../img/xboxone.png" alt="Xbox One" width="24" height="24">';
        case 'Mobile':
            return '<img src="../img/windowsphone.webp" alt="Windows Phone" width="24" height="24">';
        case 'Xbox360':
            return '<img src="../img/xbox360.png" alt="Xbox 360" width="24" height="24">';
        default:
            return htmlspecialchars($deviceType);
    }
}

function getBoxArt($game)
{
    if (isset($game['images'])) {
        foreach ($game['images'] as $image) {
            if ($image['type'] === 'BoxArt') {
                return $image['url'];
            }
        }
    }

    return '../img/default_game.jpg';
}

function getSuperHeroArt($game)
{
    if (isset($game['images'])) {
        foreach ($game['images'] as $image) {
            if ($image['type'] === 'SuperHeroArt') {
                return $image['url'];
            }
        }
    }

    return '';
}
?>
<main class="xbox-content">
    <div class="xbox-page space-y-6">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Jogos</span>
            <h1 class="xbox-hero-title">Conquistas</h1>
        </section>

        <div class="xbox-panel space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="friends-search">
                    <i class="fas fa-search text-green-200/80"></i>
                    <input
                        type="text"
                        id="achievementSearch"
                        placeholder="Buscar por Nome..."
                        class="friends-search-input" />
                    <button id="achievementSearchButton" class="friends-search-btn" aria-label="Buscar">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <label class="filter-select">
                        <select id="filterName" class="filter-select-input">
                            <option value="">Nome</option>
                            <option value="asc">A-Z</option>
                            <option value="desc">Z-A</option>
                        </select>
                        <span class="filter-chevron"><i class="fas fa-chevron-down"></i></span>
                    </label>
                    <label class="filter-select">
                        <select id="filterGamerscore" class="filter-select-input">
                            <option value="">Gamerscore</option>
                            <option value="asc">Do menor para o maior</option>
                            <option value="desc">Do maior para o menor</option>
                        </select>
                        <span class="filter-chevron"><i class="fas fa-chevron-down"></i></span>
                    </label>
                    <label class="filter-select">
                        <select id="filterLastPlayed" class="filter-select-input">
                            <option value="">Jogado pela Última Vez</option>
                            <option value="recent">Mais Recente</option>
                            <option value="oldest">Mais Antigo</option>
                        </select>
                        <span class="filter-chevron"><i class="fas fa-chevron-down"></i></span>
                    </label>
                    <label class="filter-select">
                        <select id="filterPlatform" class="filter-select-input">
                            <option value="">Plataformas</option>
                            <option value="Mobile">Mobile (Windows Phone)</option>
                            <option value="PC">PC (Windows Store)</option>
                            <option value="Win32">PC (Outros)</option>
                            <option value="Xbox360">Xbox 360</option>
                            <option value="XboxOne">Xbox One</option>
                            <option value="XboxSeries">Xbox Series</option>
                        </select>
                        <span class="filter-chevron"><i class="fas fa-chevron-down"></i></span>
                    </label>
                </div>
            </div>
        </div>

        <?php if (isset($error_message)) : ?>
            <p class="text-green-50"><?php echo htmlspecialchars($error_message); ?></p>
        <?php elseif (empty($games)) : ?>
            <p class="text-green-50">Você ainda não possui jogos com conquistas registradas.</p>
        <?php else : ?>
            <div id="achievementsList" class="friend-grid">
                <?php foreach ($games as $game) : ?>
                    <?php
                    $boxArt = getBoxArt($game);
                    $heroArt = getSuperHeroArt($game);
                    $name = $game['name'] ?? 'Título não disponível';
                    $devices = $game['devices'] ?? [];
                    $deviceIcons = !empty($devices)
                        ? implode(' ', array_map('getDeviceIcon', $devices))
                        : 'Plataforma não disponível';
                    $currentGs = $game['achievement']['currentGamerscore'] ?? 0;
                    $progress = $game['achievement']['progressPercentage'] ?? 0;
                    $lastPlayedRaw = $game['titleHistory']['lastTimePlayed'] ?? null;
                    $lastPlayed = $lastPlayedRaw ? date('d/m/Y', strtotime($lastPlayedRaw)) : 'Data não disponível';
                    $lastPlayedSort = $lastPlayedRaw ? strtotime($lastPlayedRaw) : 0;
                    $platformsAttr = !empty($devices) ? strtolower(implode(',', $devices)) : '';
                    ?>
                    <article
                        class="friend-card xbox-glass-card achievement-card"
                        data-name="<?php echo htmlspecialchars(strtolower($name)); ?>"
                        data-gamerscore="<?php echo $currentGs; ?>"
                        data-last-played="<?php echo $lastPlayedSort; ?>"
                        data-platforms="<?php echo htmlspecialchars($platformsAttr); ?>"
                        data-hero-art="<?php echo htmlspecialchars($heroArt); ?>">
                        <div class="activity-card-header">
                            <div class="friend-card-header">
                                <span class="friend-status-dot"></span>
                                <span class="friend-added">Jogado pela última vez em <?php echo htmlspecialchars($lastPlayed); ?></span>
                            </div>
                            <div class="badge-soft">Progresso: <?php echo $progress; ?>%</div>
                        </div>
                        <div class="activity-card-body achievement-body">
                            <div class="friend-avatar achievement-cover">
                                <img src="<?php echo htmlspecialchars($boxArt); ?>" alt="Capa de <?php echo htmlspecialchars($name); ?>" />
                            </div>
                            <div class="activity-details achievement-details">
                                <div class="activity-author">
                                    <div class="friend-gamertag"><?php echo htmlspecialchars($name); ?></div>
                                    <div class="friend-presence">Jogado pela última vez em <?php echo htmlspecialchars($lastPlayed); ?></div>
                                </div>
                                <div class="achievement-meta">
                                    <div class="friend-gamerscore">
                                        <strong><?php echo number_format($currentGs, 0, ',', '.'); ?></strong>
                                        <img src="../img/gs.png" alt="Gamerscore Icon" class="friend-gs-icon">
                                    </div>
                                    <div class="achievement-platforms">
                                        <span class="text-label">Plataformas:</span>
                                        <span class="platform-icons"><?php echo $deviceIcons; ?></span>
                                    </div>
                                </div>
                                <div class="achievement-progress-bar">
                                    <div class="achievement-progress-fill" style="width: <?php echo min(100, (float)$progress); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div id="achievementsPagination" class="friends-pagination"></div>
        <?php endif; ?>
    </div>
</main>
<script>
    const achievementSearch = document.getElementById('achievementSearch');
    const achievementSearchButton = document.getElementById('achievementSearchButton');
    const filterName = document.getElementById('filterName');
    const filterGamerscore = document.getElementById('filterGamerscore');
    const filterLastPlayed = document.getElementById('filterLastPlayed');
    const filterPlatform = document.getElementById('filterPlatform');
    const achievementsList = document.getElementById('achievementsList');
    const paginationContainer = document.getElementById('achievementsPagination');
    const achievementCards = achievementsList ? Array.from(achievementsList.querySelectorAll('.achievement-card')) : [];
    const PAGE_SIZE = <?php echo $items_per_page; ?>;
    let currentPage = 1;

    function applyFilters() {
        const term = achievementSearch.value.toLowerCase();
        const platform = filterPlatform.value.toLowerCase();

        return achievementCards.filter((card) => {
            const matchesName = card.getAttribute('data-name').includes(term);
            const platforms = card.getAttribute('data-platforms');
            const matchesPlatform = !platform || (platforms && platforms.split(',').includes(platform));
            return matchesName && matchesPlatform;
        });
    }

    function applySorting(list) {
        const nameSort = filterName.value;
        const gsSort = filterGamerscore.value;
        const lastPlayedSort = filterLastPlayed.value;
        const sorted = [...list];

        if (nameSort) {
            sorted.sort((a, b) => {
                const nameA = a.getAttribute('data-name');
                const nameB = b.getAttribute('data-name');
                return nameSort === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
            });
        }

        if (gsSort) {
            sorted.sort((a, b) => {
                const gsA = parseInt(a.getAttribute('data-gamerscore'), 10) || 0;
                const gsB = parseInt(b.getAttribute('data-gamerscore'), 10) || 0;
                return gsSort === 'asc' ? gsA - gsB : gsB - gsA;
            });
        }

        if (lastPlayedSort) {
            sorted.sort((a, b) => {
                const dateA = parseInt(a.getAttribute('data-last-played'), 10) || 0;
                const dateB = parseInt(b.getAttribute('data-last-played'), 10) || 0;
                return lastPlayedSort === 'recent' ? dateB - dateA : dateA - dateB;
            });
        }

        return sorted;
    }

    function renderPagination(totalPages) {
        paginationContainer.innerHTML = '';
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
                    currentPage = page;
                    updateAchievements();
                });
            }
            return button;
        };

        const addSeparator = () => {
            const separator = document.createElement('span');
            separator.className = 'pagination-separator';
            separator.textContent = '';
            paginationContainer.appendChild(separator);
        };

        const maxVisible = 5;
        const halfWindow = Math.floor(maxVisible / 2);
        let startPage = Math.max(1, currentPage - halfWindow);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        const hasPrev = currentPage > 1;
        const hasNext = currentPage < totalPages;

        paginationContainer.appendChild(
            createButton('Anterior', currentPage - 1, {
                disabled: !hasPrev
            })
        );

        addSeparator();

        for (let page = startPage; page <= endPage; page++) {
            paginationContainer.appendChild(createButton(page, page, {
                isActive: page === currentPage
            }));
            if (page < endPage) addSeparator();
        }

        addSeparator();

        paginationContainer.appendChild(
            createButton('Próximo', currentPage + 1, {
                disabled: !hasNext
            })
        );
    }

    function updateAchievements() {
        if (!achievementsList) return;

        const filtered = applyFilters();
        const sorted = applySorting(filtered);
        const totalPages = Math.max(1, Math.ceil(sorted.length / PAGE_SIZE));
        currentPage = Math.min(currentPage, totalPages);

        achievementCards.forEach((card) => {
            card.style.display = 'none';
        });

        const start = (currentPage - 1) * PAGE_SIZE;
        const visible = sorted.slice(start, start + PAGE_SIZE);
        visible.forEach((card) => {
            card.style.display = '';
        });

        renderPagination(totalPages);
    }

    // Background dinâmico ao passar mouse sobre os cards
    if (achievementsList) {
        const xboxContent = document.querySelector('.xbox-content');
        const defaultBg = window.getComputedStyle(xboxContent).backgroundImage;

        achievementCards.forEach((card) => {
            card.addEventListener('mouseenter', () => {
                const heroArt = card.getAttribute('data-hero-art');
                if (heroArt && xboxContent) {
                    xboxContent.style.backgroundImage = `
                        linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 20, 10, 0.95)),
                        url('${heroArt}')
                    `;
                    xboxContent.style.backgroundSize = 'cover';
                    xboxContent.style.backgroundPosition = 'center';
                    xboxContent.style.transition = 'background-image 0.5s ease-in-out';
                }
            });

            card.addEventListener('mouseleave', () => {
                if (xboxContent) {
                    xboxContent.style.backgroundImage = defaultBg;
                }
            });
        });
    }

    if (achievementsList) {
        achievementSearch.addEventListener('input', () => {
            currentPage = 1;
            updateAchievements();
        });

        achievementSearchButton.addEventListener('click', () => {
            currentPage = 1;
            updateAchievements();
        });

        [filterName, filterGamerscore, filterLastPlayed, filterPlatform].forEach((select) => {
            select.addEventListener('change', () => {
                currentPage = 1;
                updateAchievements();
            });
        });

        updateAchievements();
    }
</script>
<?php include('../includes/footer.php'); ?>