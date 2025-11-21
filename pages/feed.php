<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';

    $endpoint = "activity/feed";
    $response = openXBLRequest($endpoint);
    $activityItems = $response['activityItems'] ?? [];
?>
<main class="xbox-content">
    <div class="xbox-page space-y-6">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Comunidade</span>
            <h1 class="xbox-hero-title">Feed de Atividades</h1>
            <p class="xbox-hero-subtitle">
                Veja capturas, clips e atualizações recentes com cartões de vidro líquido e navegação alinhada ao restante da experiência.
            </p>
        </section>

        <div class="xbox-panel space-y-4">
            <div class="friends-search">
                <i class="fas fa-search text-green-200/80"></i>
                <input
                    type="text"
                    id="feedSearch"
                    placeholder="Buscar por Gamertag..."
                    class="friends-search-input"
                />
                <button id="feedSearchButton" class="friends-search-btn" aria-label="Buscar">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($activityItems)) : ?>
            <div id="feedList" class="friend-grid">
                <?php foreach ($activityItems as $item) : ?>
                    <?php
                        $author = $item['authorInfo'] ?? [];
                        $avatar = !empty($author['imageUrl']) ? $author['imageUrl'] : '../img/default_avatar.jpg';
                        $gamertag = $author['modernGamertag'] ?? 'Gamertag indisponível';
                        $secondary = $author['secondName'] ?? 'Nome não disponível';
                        $description = $item['description'] ?? 'Atividade';
                        $itemText = $item['itemText'] ?? 'Texto não disponível';
                        $timeline = $item['timeline']['timelineName'] ?? 'Timeline não disponível';
                        $dateRaw = $item['date'] ?? null;
                        $formattedDate = $dateRaw ? date('d/m/Y H:i', strtotime($dateRaw)) : 'Data não disponível';
                        $comments = isset($item['numComments']) ? $item['numComments'] : 'Sem comentários';
                        $liked = !empty($item['hasLiked']);
                    ?>
                    <article class="friend-card xbox-glass-card activity-card" data-gamertag="<?php echo htmlspecialchars(strtolower($gamertag)); ?>">
                        <div class="activity-card-header">
                            <div class="friend-card-header">
                                <span class="friend-status-dot"></span>
                                <span class="friend-added">Compartilhado em <?php echo htmlspecialchars($timeline); ?></span>
                            </div>
                            <span class="activity-date">Publicado em <?php echo $formattedDate; ?></span>
                        </div>
                        <div class="activity-card-body">
                            <div class="friend-avatar">
                                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar de <?php echo htmlspecialchars($gamertag); ?>" />
                            </div>
                            <div class="activity-details">
                                <div class="activity-author">
                                    <div class="friend-gamertag"><?php echo htmlspecialchars($gamertag); ?></div>
                                    <div class="friend-presence"><?php echo htmlspecialchars($secondary); ?></div>
                                </div>
                                <div class="activity-description"><?php echo htmlspecialchars($description); ?></div>
                                <div class="activity-text"><?php echo htmlspecialchars($itemText); ?></div>
                                <div class="activity-meta">
                                    <span>Comentários: <?php echo htmlspecialchars($comments); ?></span>
                                    <span class="activity-like">Likes: <?php echo $liked ? '✔️' : '❌'; ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div id="feedPagination" class="friends-pagination"></div>
        <?php else : ?>
            <p class="text-green-50">Nenhuma atividade disponível no momento.</p>
        <?php endif; ?>
    </div>
</main>
<script>
    const feedSearchInput = document.getElementById('feedSearch');
    const feedSearchButton = document.getElementById('feedSearchButton');
    const feedList = document.getElementById('feedList');
    const paginationContainer = document.getElementById('feedPagination');
    const feedCards = feedList ? Array.from(feedList.querySelectorAll('.activity-card')) : [];
    const PAGE_SIZE = 12;
    let currentPage = 1;

    function filterFeed() {
        const term = feedSearchInput.value.toLowerCase();
        return feedCards.filter((card) => card.getAttribute('data-gamertag').includes(term));
    }

    function renderPagination(totalPages) {
        paginationContainer.innerHTML = '';
        if (totalPages <= 1) return;

        const createButton = (label, page, { isActive = false, disabled = false } = {}) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.className = `pagination-btn ${isActive ? 'active' : ''} ${disabled ? 'disabled' : ''}`;
            if (!disabled) {
                button.addEventListener('click', () => {
                    currentPage = page;
                    updateFeed();
                });
            }
            return button;
        };

        const addSeparator = () => {
            const separator = document.createElement('span');
            separator.className = 'pagination-separator';
            separator.textContent = '|';
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
            createButton('Anterior', currentPage - 1, { disabled: !hasPrev })
        );

        addSeparator();

        for (let page = startPage; page <= endPage; page++) {
            paginationContainer.appendChild(createButton(page, page, { isActive: page === currentPage }));
            if (page < endPage) addSeparator();
        }

        addSeparator();

        paginationContainer.appendChild(
            createButton('Próximo', currentPage + 1, { disabled: !hasNext })
        );
    }

    function updateFeed() {
        if (!feedList) return;

        const filtered = filterFeed();
        const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        currentPage = Math.min(currentPage, totalPages);

        feedCards.forEach((card) => {
            card.style.display = 'none';
        });

        const start = (currentPage - 1) * PAGE_SIZE;
        const visible = filtered.slice(start, start + PAGE_SIZE);
        visible.forEach((card) => {
            card.style.display = '';
        });

        renderPagination(totalPages);
    }

    if (feedList) {
        feedSearchInput.addEventListener('input', () => {
            currentPage = 1;
            updateFeed();
        });

        feedSearchButton.addEventListener('click', () => {
            currentPage = 1;
            updateFeed();
        });

        updateFeed();
    }
</script>
<?php include('../includes/footer.php'); ?>
