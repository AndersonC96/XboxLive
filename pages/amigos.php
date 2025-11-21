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

    $endpoint = "friends";
    $response = openXBLRequest($endpoint);

    if ($response && isset($response['people']) && is_array($response['people'])) {
        $friends = $response['people'];
    } else {
        $friends = [];
    }
?>
<main class="xbox-content">
    <div class="xbox-page space-y-6">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Comunidade</span>
            <h1 class="xbox-hero-title">Lista de Amigos</h1>
            <p class="xbox-hero-subtitle">Encontre, ordene e navegue pela sua comunidade com cartões em vidro líquido e controles alinhados à navegação.</p>
        </section>

        <div class="xbox-panel space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="friends-search">
                    <i class="fas fa-search text-green-200/80"></i>
                    <input
                        type="text"
                        id="filterGamertag"
                        placeholder="Buscar por Gamertag..."
                        class="friends-search-input"
                    />
                    <button id="searchButton" class="friends-search-btn" aria-label="Buscar">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <label class="filter-select">
                        <select id="filterDate" class="filter-select-input">
                            <option value="">Data de Amizade</option>
                            <option value="oldest">Mais Antigo</option>
                            <option value="newest">Mais Recente</option>
                        </select>
                        <span class="filter-chevron"><i class="fas fa-chevron-down"></i></span>
                    </label>
                    <label class="filter-select">
                        <select id="filterGamerscore" class="filter-select-input">
                            <option value="">Gamerscore</option>
                            <option value="asc">Ordem Crescente</option>
                            <option value="desc">Ordem Decrescente</option>
                        </select>
                        <span class="filter-chevron"><i class="fas fa-chevron-down"></i></span>
                    </label>
                </div>
            </div>
        </div>

        <?php if (!empty($friends)) : ?>
            <div id="friendsList" class="friend-grid">
                <?php foreach ($friends as $friend) : ?>
                    <?php
                        $avatar = !empty($friend['displayPicRaw']) ? $friend['displayPicRaw'] : '../img/default_avatar.jpg';
                        $addedDate = new DateTime($friend['addedDateTimeUtc']);
                        $formattedDate = $addedDate->format('d/m/Y');
                    ?>
                    <article class="friend-card xbox-glass-card" data-gamertag="<?php echo htmlspecialchars($friend['gamertag']); ?>" data-added="<?php echo (new DateTime($friend['addedDateTimeUtc']))->format('Y-m-d'); ?>" data-gamerscore="<?php echo $friend['gamerScore']; ?>">
                        <div class="friend-card-header">
                            <span class="friend-status-dot"></span>
                            <span class="friend-added">Amigo desde <?php echo $formattedDate; ?></span>
                        </div>
                        <div class="friend-card-body">
                            <div class="friend-avatar">
                                <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo htmlspecialchars($friend['displayName']); ?>" />
                            </div>
                            <div class="friend-details">
                                <div class="friend-gamertag"><?php echo htmlspecialchars($friend['gamertag']); ?></div>
                                <div class="friend-presence"><?php echo $friend['presenceText']; ?></div>
                                <div class="friend-gamerscore">
                                    <span>Gamerscore</span>
                                    <strong><?php echo number_format($friend['gamerScore'], 0, ',', '.'); ?></strong>
                                    <img src="../img/gs.png" alt="Gamerscore Icon" class="friend-gs-icon">
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div id="friendsPagination" class="friends-pagination"></div>
        <?php else : ?>
            <p class="text-green-50">Você não tem amigos adicionados no momento.</p>
        <?php endif; ?>
    </div>
</main>
<script>
    const searchInput = document.getElementById('filterGamertag');
    const searchButton = document.getElementById('searchButton');
    const filterDate = document.getElementById('filterDate');
    const filterGamerscore = document.getElementById('filterGamerscore');
    const friendsList = document.getElementById('friendsList');
    const paginationContainer = document.getElementById('friendsPagination');
    const friendCards = friendsList ? Array.from(friendsList.querySelectorAll('.friend-card')) : [];
    const PAGE_SIZE = 12;
    let currentPage = 1;

    function applyFilters() {
        const gamertagTerm = searchInput.value.toLowerCase();
        return friendCards.filter((card) => {
            const gamertag = card.getAttribute('data-gamertag').toLowerCase();
            return gamertag.includes(gamertagTerm);
        });
    }

    function applySorting(list) {
        const dateValue = filterDate.value;
        const gamerscoreValue = filterGamerscore.value;
        const sorted = [...list];

        if (dateValue) {
            sorted.sort((a, b) => {
                const dateA = new Date(a.getAttribute('data-added'));
                const dateB = new Date(b.getAttribute('data-added'));
                return dateValue === 'newest' ? dateB - dateA : dateA - dateB;
            });
        }

        if (gamerscoreValue) {
            sorted.sort((a, b) => {
                const scoreA = parseInt(a.getAttribute('data-gamerscore'), 10);
                const scoreB = parseInt(b.getAttribute('data-gamerscore'), 10);
                return gamerscoreValue === 'asc' ? scoreA - scoreB : scoreB - scoreA;
            });
        }

        return sorted;
    }

    function renderPagination(totalPages) {
        paginationContainer.innerHTML = '';
        if (totalPages <= 1) {
            return;
        }

        const createButton = (label, page, isActive = false) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.className = `pagination-btn ${isActive ? 'active' : ''}`;
            button.addEventListener('click', () => {
                currentPage = page;
                updateFriends();
            });
            return button;
        };

        for (let page = 1; page <= totalPages; page++) {
            paginationContainer.appendChild(createButton(page, page, page === currentPage));
        }
    }

    function updateFriends() {
        if (!friendsList) return;

        const filtered = applyFilters();
        const sorted = applySorting(filtered);
        const totalPages = Math.max(1, Math.ceil(sorted.length / PAGE_SIZE));
        currentPage = Math.min(currentPage, totalPages);

        friendCards.forEach((card) => {
            card.style.display = 'none';
        });

        const start = (currentPage - 1) * PAGE_SIZE;
        const visibleCards = sorted.slice(start, start + PAGE_SIZE);
        visibleCards.forEach((card) => {
            card.style.display = '';
        });

        renderPagination(totalPages);
    }

    if (friendsList) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            updateFriends();
        });

        searchButton.addEventListener('click', () => {
            currentPage = 1;
            updateFriends();
        });

        filterDate.addEventListener('change', () => {
            currentPage = 1;
            updateFriends();
        });

        filterGamerscore.addEventListener('change', () => {
            currentPage = 1;
            updateFriends();
        });

        updateFriends();
    }
</script>
<?php include('../includes/footer.php'); ?>
