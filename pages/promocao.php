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

    $endpoint = "marketplace/deals";
    $response = openXBLRequest($endpoint);
    $products = isset($response['Products']) ? $response['Products'] : [];
    $items = isset($response['Items']) ? $response['Items'] : [];

    if (empty($products)) {
        echo "Nenhum jogo encontrado.";
        exit;
    }

    function getPredictedScore($productId, $items)
    {
        foreach ($items as $item) {
            if ($item['Id'] === $productId && isset($item['PredictedScore'])) {
                return $item['PredictedScore'];
            }
        }

        return 'N/A';
    }

    function getBoxArtImage($images)
    {
        foreach ($images as $image) {
            if ($image['ImagePurpose'] === 'BoxArt') {
                return $image['Uri'];
            }
        }

        return '';
    }

    $items_per_page = 12;
    $total_items = count($products);
    $total_pages = ceil($total_items / $items_per_page);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $current_page_products = array_slice($products, $offset, $items_per_page);
?>
<main class="xbox-content">
    <div class="xbox-page space-y-6">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Ofertas</span>
            <h1 class="xbox-hero-title">Promoções</h1>
            <p class="xbox-hero-subtitle">Encontre jogos em promoção com o mesmo visual de vidro líquido, busca e paginação temática.</p>
        </section>

        <div class="xbox-panel space-y-4">
            <div class="friends-search">
                <i class="fas fa-search text-green-200/80"></i>
                <input
                    type="text"
                    id="gamesSearch"
                    placeholder="Buscar por título..."
                    class="friends-search-input"
                />
                <button id="gamesSearchButton" class="friends-search-btn" aria-label="Buscar">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($current_page_products)) : ?>
            <div id="gamesList" class="friend-grid">
                <?php foreach ($current_page_products as $product) : ?>
                    <?php
                        $boxArtImage = getBoxArtImage($product['LocalizedProperties'][0]['Images'] ?? []);
                        $title = $product['LocalizedProperties'][0]['ProductTitle'] ?? 'Título não disponível';
                        $description = $product['LocalizedProperties'][0]['ProductDescription'] ?? 'Descrição não disponível';
                        $developer = $product['LocalizedProperties'][0]['DeveloperName'] ?? 'Desconhecida';
                        $publisher = $product['LocalizedProperties'][0]['PublisherName'] ?? 'Desconhecida';
                        $category = $product['Properties']['Category'] ?? 'N/A';
                        $predictedScore = getPredictedScore($product['ProductId'], $items);
                        $originalPrice = 'Não disponível';
                        $discountedPrice = 'Não disponível';

                        if (isset($product['DisplaySkuAvailabilities'][0]['OrderManagementData']['Price']['MSRP'])) {
                            $msrpValue = $product['DisplaySkuAvailabilities'][0]['OrderManagementData']['Price']['MSRP'];
                            $originalPrice = '$' . number_format($msrpValue, 2);
                        }

                        if (isset($product['DisplaySkuAvailabilities'][0]['OrderManagementData']['Price']['ListPrice'])) {
                            $listValue = $product['DisplaySkuAvailabilities'][0]['OrderManagementData']['Price']['ListPrice'];
                            $discountedPrice = '$' . number_format($listValue, 2);
                        }
                    ?>
                    <article class="friend-card xbox-glass-card game-card" data-title="<?php echo htmlspecialchars(strtolower($title)); ?>">
                        <div class="game-card-body">
                            <div class="game-cover">
                                <?php if (!empty($boxArtImage)) : ?>
                                    <img src="<?php echo 'https:' . $boxArtImage; ?>" alt="Capa de <?php echo htmlspecialchars($title); ?>">
                                <?php else : ?>
                                    <img src="../img/placeholder.png" alt="Imagem não disponível">
                                <?php endif; ?>
                            </div>
                            <div class="game-details">
                                <div class="game-title"><?php echo htmlspecialchars($title); ?></div>
                                <div class="game-meta">
                                    <span class="game-badge">Score: <?php echo htmlspecialchars($predictedScore); ?></span>
                                    <span class="game-badge">Desenvolvedora: <?php echo htmlspecialchars($developer); ?></span>
                                    <span class="game-badge">Publisher: <?php echo htmlspecialchars($publisher); ?></span>
                                    <span class="game-badge">Categoria: <?php echo htmlspecialchars($category); ?></span>
                                </div>
                                <p class="game-description"><?php echo htmlspecialchars($description); ?></p>
                                <div class="game-price">Preço Original: <strong><?php echo htmlspecialchars($originalPrice); ?></strong></div>
                                <div class="game-price">Preço com Desconto: <strong><?php echo htmlspecialchars($discountedPrice); ?></strong></div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="friends-pagination">
                <?php
                    $maxVisible = 5;
                    $halfWindow = floor($maxVisible / 2);
                    $startPage = max(1, $page - $halfWindow);
                    $endPage = min($total_pages, $startPage + $maxVisible - 1);

                    if (($endPage - $startPage + 1) < $maxVisible) {
                        $startPage = max(1, $endPage - $maxVisible + 1);
                    }

                    $hasPrev = $page > 1;
                    $hasNext = $page < $total_pages;
                ?>
                <a class="pagination-btn <?php echo $hasPrev ? '' : 'disabled'; ?>" href="<?php echo $hasPrev ? '?page=' . ($page - 1) : 'javascript:void(0);'; ?>">Anterior</a>
                <span class="pagination-separator">|</span>
                <?php for ($p = $startPage; $p <= $endPage; $p++) : ?>
                    <a class="pagination-btn <?php echo $p === $page ? 'active' : ''; ?>" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                    <?php if ($p < $endPage) : ?>
                        <span class="pagination-separator">|</span>
                    <?php endif; ?>
                <?php endfor; ?>
                <span class="pagination-separator">|</span>
                <a class="pagination-btn <?php echo $hasNext ? '' : 'disabled'; ?>" href="<?php echo $hasNext ? '?page=' . ($page + 1) : 'javascript:void(0);'; ?>">Próximo</a>
            </div>
        <?php else : ?>
            <p class="text-green-50">Nenhum detalhe de jogo encontrado.</p>
        <?php endif; ?>
    </div>
</main>
<script>
    const gamesSearchInput = document.getElementById('gamesSearch');
    const gamesSearchButton = document.getElementById('gamesSearchButton');
    const gamesList = document.getElementById('gamesList');
    const gameCards = gamesList ? Array.from(gamesList.querySelectorAll('.game-card')) : [];

    function filterGames() {
        const term = gamesSearchInput.value.toLowerCase();
        gameCards.forEach((card) => {
            const title = card.getAttribute('data-title') || '';
            card.style.display = title.includes(term) ? '' : 'none';
        });
    }

    if (gamesList) {
        gamesSearchInput.addEventListener('input', filterGames);
        gamesSearchButton.addEventListener('click', filterGames);
    }
</script>
<?php include('../includes/footer.php'); ?>
