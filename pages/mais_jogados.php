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
    $endpoint = "marketplace/most-played";
    $response = openXBLRequest($endpoint);
    $products = isset($response['Products']) ? $response['Products'] : [];
    $items = isset($response['Items']) ? $response['Items'] : [];
    if (empty($products)) {
        echo "Nenhum jogo mais jogado encontrado.";
        exit;
    }
    // Função para buscar o PredictedScore baseado no ProductId
    function getPredictedScore($productId, $items) {
        foreach ($items as $item) {
            if ($item['Id'] === $productId && isset($item['PredictedScore'])) {
                return $item['PredictedScore'];
            }
        }
        return 'N/A';
    }
    $items_per_page = 10;
    $total_items = count($products);
    $total_pages = ceil($total_items / $items_per_page);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $current_page_products = array_slice($products, $offset, $items_per_page);
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Jogos Mais Jogados</h1>
    <?php if (!empty($current_page_products)) : ?>
        <ul>
            <?php foreach ($current_page_products as $product) : ?>
                <?php $predictedScore = getPredictedScore($product['ProductId'], $items); ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?php
                                    foreach ($product['LocalizedProperties'][0]['Images'] as $image) {
                                        if ($image['ImagePurpose'] === 'BoxArt') {
                                            echo $image['Uri'];
                                            break;
                                        }
                                    }
                                    ?>" alt="Imagem do jogo" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo $predictedScore . '. ' . htmlspecialchars($product['LocalizedProperties'][0]['ProductTitle']); ?></p>
                            <p class="text-sm text-gray-400">Desenvolvedora: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['DeveloperName']); ?></p>
                            <p class="text-sm text-gray-400">Publisher: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['PublisherName']); ?></p>
                            <p class="text-sm text-gray-400">Descrição: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductDescription']); ?></p>
                            <p class="text-sm text-gray-400">Categoria: <?php echo htmlspecialchars($product['Properties']['Category'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-4">
            <?php if ($page > 1) : ?>
                <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-300 rounded">Anterior</a>
            <?php endif; ?>
            <?php if ($page < $total_pages) : ?>
                <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-300 rounded">Próxima</a>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <p>Nenhum detalhe de jogo encontrado.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>