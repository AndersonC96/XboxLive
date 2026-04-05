<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

include('../includes/header.php');
include('../includes/navbar.php');
require '../config/db.php';
require_once '../config/api.php';
if (!isset($_SESSION['user_id'])) {
    echo "Erro: Usuário não está logado.";
    exit;
}

$endpoint = "friends/blocked";
$response = openXBLRequest($endpoint);

if ($response && isset($response['users']) && is_array($response['users'])) {
    $blockedFriends = $response['users'];
} else {
    $blockedFriends = [];
}
?>
<main class="xbox-content">
    <div class="xbox-page space-y-6">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Comunidade</span>
            <h1 class="xbox-hero-title">Amigos Bloqueados</h1>
        </section>

        <div class="xbox-panel space-y-4">
            <div class="friends-search">
                <i class="fas fa-search text-green-200/80"></i>
                <input
                    type="text"
                    id="filterBlockedGamertag"
                    placeholder="Buscar por Gamertag..."
                    class="friends-search-input" />
                <button id="blockedSearchButton" class="friends-search-btn" aria-label="Buscar">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($blockedFriends)) : ?>
            <div id="blockedList" class="friend-grid">
                <?php foreach ($blockedFriends as $blockedFriend) : ?>
                    <?php
                    $avatar = !empty($blockedFriend['displayPicRaw']) ? $blockedFriend['displayPicRaw'] : '../img/default_avatar.jpg';
                    $gamertag = isset($blockedFriend['gamertag']) ? $blockedFriend['gamertag'] : ($blockedFriend['displayName'] ?? 'Usuário');
                    $realName = $blockedFriend['realName'] ?? '';
                    $bio = $blockedFriend['bio'] ?? '';
                    ?>
                    <article
                        class="friend-card xbox-glass-card"
                        data-gamertag="<?php echo htmlspecialchars($gamertag); ?>">
                        <div class="friend-card-header">
                            <span class="friend-status-dot"></span>
                            <span class="friend-added">Status: Bloqueado</span>
                        </div>
                        <div class="friend-card-body">
                            <div class="friend-avatar">
                                <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo htmlspecialchars($gamertag); ?>" />
                            </div>
                            <div class="friend-details">
                                <div class="friend-gamertag"><?php echo htmlspecialchars($gamertag); ?></div>
                                <?php if (!empty($realName)) : ?>
                                    <div class="friend-presence"><?php echo htmlspecialchars($realName); ?></div>
                                <?php elseif (!empty($bio)) : ?>
                                    <div class="friend-presence"><?php echo htmlspecialchars($bio); ?></div>
                                <?php else : ?>
                                    <div class="friend-presence">Usuário bloqueado</div>
                                <?php endif; ?>
                                <div class="friend-gamerscore">
                                    <span>Bloqueio</span>
                                    <strong>Aplicado</strong>
                                    <i class="fas fa-ban friend-gs-icon" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div id="blockedPagination" class="friends-pagination"></div>
        <?php else : ?>
            <p class="text-green-50">Você não tem amigos bloqueados no momento.</p>
        <?php endif; ?>
    </div>
</main>
<script>
    const blockedSearchInput = document.getElementById('filterBlockedGamertag');
    const blockedSearchButton = document.getElementById('blockedSearchButton');
    const blockedList = document.getElementById('blockedList');
    const blockedPagination = document.getElementById('blockedPagination');
    const blockedCards = blockedList ? Array.from(blockedList.querySelectorAll('.friend-card')) : [];
    const BLOCKED_PAGE_SIZE = 12;
    let blockedCurrentPage = 1;

    function applyBlockedFilters() {
        const gamertagTerm = blockedSearchInput.value.toLowerCase();
        return blockedCards.filter((card) => {
            const gamertag = card.getAttribute('data-gamertag').toLowerCase();
            return gamertag.includes(gamertagTerm);
        });
    }

    function renderBlockedPagination(totalPages) {
        blockedPagination.innerHTML = '';
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
                    blockedCurrentPage = page;
                    updateBlocked();
                });
            }
            return button;
        };

        const addSeparator = () => {
            const separator = document.createElement('span');
            separator.className = 'pagination-separator';
            separator.textContent = '|';
            blockedPagination.appendChild(separator);
        };

        const maxVisible = 5;
        const halfWindow = Math.floor(maxVisible / 2);
        let startPage = Math.max(1, blockedCurrentPage - halfWindow);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        const hasPrev = blockedCurrentPage > 1;
        const hasNext = blockedCurrentPage < totalPages;

        blockedPagination.appendChild(
            createButton('Anterior', blockedCurrentPage - 1, {
                disabled: !hasPrev
            })
        );

        addSeparator();

        for (let page = startPage; page <= endPage; page++) {
            blockedPagination.appendChild(createButton(page, page, {
                isActive: page === blockedCurrentPage
            }));
            if (page < endPage) addSeparator();
        }

        addSeparator();

        blockedPagination.appendChild(
            createButton('Próximo', blockedCurrentPage + 1, {
                disabled: !hasNext
            })
        );
    }

    function updateBlocked() {
        if (!blockedList) return;

        const filtered = applyBlockedFilters();
        const totalPages = Math.max(1, Math.ceil(filtered.length / BLOCKED_PAGE_SIZE));
        blockedCurrentPage = Math.min(blockedCurrentPage, totalPages);

        blockedCards.forEach((card) => {
            card.style.display = 'none';
        });

        const start = (blockedCurrentPage - 1) * BLOCKED_PAGE_SIZE;
        const visibleCards = filtered.slice(start, start + BLOCKED_PAGE_SIZE);
        visibleCards.forEach((card) => {
            card.style.display = '';
        });

        renderBlockedPagination(totalPages);
    }

    if (blockedList) {
        blockedSearchInput.addEventListener('input', () => {
            blockedCurrentPage = 1;
            updateBlocked();
        });

        blockedSearchButton.addEventListener('click', () => {
            blockedCurrentPage = 1;
            updateBlocked();
        });

        updateBlocked();
    }
</script>
<?php include('../includes/footer.php'); ?>