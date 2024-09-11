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
    $endpoint = "marketplace/coming-soon";
    $response = openXBLRequest($endpoint);
    $products = isset($response['Products']) ? $response['Products'] : [];
    if (empty($products)) {
        echo "Nenhum jogo em breve encontrado.";
        exit;
    }
    $items_per_page = 10;
    $total_items = count($products);
    $total_pages = ceil($total_items / $items_per_page);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $current_page_products = array_slice($products, $offset, $items_per_page);
    function formatarDataLancamento($data) {
        $date = new DateTime($data);
        return $date->format('d/m/Y');
    }
    function getBoxArtImage($images) {
        foreach ($images as $image) {
            if ($image['ImagePurpose'] === 'BoxArt') {
                return $image['Uri'];
            }
        }
        return '';
    }
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Jogos que Estão por Vir</h1>
    <?php if (!empty($current_page_products)) : ?>
        <ul>
            <?php foreach ($current_page_products as $product) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo getBoxArtImage($product['LocalizedProperties'][0]['Images']); ?>" alt="Imagem do jogo" class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductTitle']); ?></p>
                            <p class="text-sm text-gray-400">
                                Preço:
                                <?php
                                    if (isset($product['DisplaySkuAvailabilities'][0]['OrderManagementData']['Price']['ListPrice'])) {
                                        echo '$' . number_format($product['DisplaySkuAvailabilities'][0]['OrderManagementData']['Price']['ListPrice'], 2);
                                    } else {
                                        echo 'Não disponível';
                                    }
                                ?>
                            </p>
                            <p class="text-sm text-gray-400">Lançamento: <?php echo formatarDataLancamento($product['MarketProperties'][0]['OriginalReleaseDate']); ?></p>
                            <p class="text-sm text-gray-400">Descrição: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductDescription']); ?></p>
                            <p class="text-sm text-gray-400">Desenvolvedora: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['DeveloperName']); ?></p>
                            <p class="text-sm text-gray-400">Publisher: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['PublisherName']); ?></p>
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