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
    $stmt = $pdo->prepare("SELECT game_id FROM pc_gamepass");
    $stmt->execute();
    $game_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($game_ids)) {
        echo "Nenhum jogo encontrado no banco de dados.";
        exit;
    }
    $items_per_page = 10;
    $total_items = count($game_ids);
    $total_pages = ceil($total_items / $items_per_page);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $current_page_ids = array_slice($game_ids, $offset, $items_per_page);
    $endpoint = "marketplace/details";
    $body = [
        "products" => implode(',', $current_page_ids)
    ];
    $response = openXBLPostRequest($endpoint, $body);
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Todos os Jogos do PC Game Pass </h1>
    <?php if (!empty($response['Products'])) : ?>
        <ul>
            <?php foreach ($response['Products'] as $product) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <?php
                            $boxArtImage = null;
                            if (isset($product['LocalizedProperties'][0]['Images']) && is_array($product['LocalizedProperties'][0]['Images'])) {
                                foreach ($product['LocalizedProperties'][0]['Images'] as $image) {
                                    if ($image['ImagePurpose'] === 'BoxArt') {
                                        $boxArtImage = $image['Uri'];
                                        break;
                                    }
                                }
                            }
                        ?>
                        <?php if ($boxArtImage): ?>
                            <img src="<?php echo 'https:' . $boxArtImage; ?>" alt="Imagem do jogo" class="w-16 h-16 rounded-full">
                        <?php else: ?>
                            <img src="../img/placeholder.png" alt="Imagem não disponível" class="w-16 h-16 rounded-full">
                        <?php endif; ?>
                        <div>
                            <p class="text-xl"><?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductTitle'] ?? 'Título não disponível'); ?></p>
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
                            <p class="text-sm text-gray-400">Descrição: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductDescription'] ?? 'Descrição não disponível'); ?></p>
                            <p class="text-sm text-gray-400">Desenvolvedora: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['DeveloperName'] ?? 'Desconhecida'); ?></p>
                            <p class="text-sm text-gray-400">Publisher: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['PublisherName'] ?? 'Desconhecida'); ?></p>
                            <p class="text-sm text-gray-400">Franquia: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['Franchises'][0] ?? 'Não disponível'); ?></p>
                            <p class="text-sm text-gray-400">Categoria: <?php echo htmlspecialchars($product['Properties']['Category'] ?? 'Não disponível'); ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Nenhum detalhe de jogo encontrado.</p>
    <?php endif; ?>
    <div class="mt-4">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-300 rounded">Anterior</a>
        <?php endif; ?>
        <?php if ($page < $total_pages) : ?>
            <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-300 rounded">Próxima</a>
        <?php endif; ?>
    </div>
</div>
<?php include('../includes/footer.php'); ?>