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

// Endpoint para pegar os jogos mais jogados
$endpoint = "marketplace/most-played";
$response = openXBLRequest($endpoint);

// Verificar se há produtos na resposta dentro da chave "Products"
$products = isset($response['Products']) ? $response['Products'] : [];
$items = isset($response['Items']) ? $response['Items'] : [];

if (empty($products)) {
    echo "Nenhum jogo mais jogado encontrado.";
    exit;
}

// Paginação
$items_per_page = 10;
$total_items = count($products);
$total_pages = ceil($total_items / $items_per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;
$current_page_products = array_slice($products, $offset, $items_per_page);

// Função para encontrar o PredictedScore baseado no ProductId
function getPredictedScore($productId, $items)
{
    foreach ($items as $item) {
        if ($item['Id'] === $productId) {
            return $item['PredictedScore'];
        }
    }
    return 'N/A'; // Retorna N/A se não encontrar o PredictedScore
}

?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Jogos Mais Jogados</h1>

    <?php if (!empty($current_page_products)) : ?>
        <ul>
            <?php foreach ($current_page_products as $product) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <!-- Imagem do jogo -->
                        <img src="<?php
                                    foreach ($product['LocalizedProperties'][0]['Images'] as $image) {
                                        if ($image['ImagePurpose'] === 'BoxArt') {
                                            echo $image['Uri'];
                                            break;
                                        }
                                    }
                                    ?>" alt="Imagem do jogo" class="w-16 h-16 rounded-full">

                        <div>
                            <!-- Título do jogo -->
                            <p class="text-xl"><?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductTitle']); ?></p>

                            <!-- Desenvolvedora -->
                            <p class="text-sm text-gray-400">Desenvolvedora: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['DeveloperName']); ?></p>

                            <!-- Publisher -->
                            <p class="text-sm text-gray-400">Publisher: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['PublisherName']); ?></p>

                            <!-- Descrição -->
                            <p class="text-sm text-gray-400">Descrição: <?php echo htmlspecialchars($product['LocalizedProperties'][0]['ProductDescription']); ?></p>

                            <!-- Data de lançamento -->
                            <p class="text-sm text-gray-400">Lançamento:
                                <?php
                                if (isset($product['MarketProperties']['OriginalReleaseDate'])) {
                                    $releaseDate = new DateTime($product['MarketProperties']['OriginalReleaseDate']);
                                    echo $releaseDate->format('d/m/Y');
                                } else {
                                    echo 'Data não disponível';
                                }
                                ?>
                            </p>

                            <!-- Média dos últimos 7 dias -->
                            <p class="text-sm text-gray-400">Média dos últimos 7 dias:
                                <?php
                                $predictedScore = getPredictedScore($product['ProductId'], $items);
                                echo htmlspecialchars($predictedScore['Average7Days'] ?? 'N/A');
                                ?>
                            </p>

                            <!-- Média dos últimos 30 dias -->
                            <p class="text-sm text-gray-400">Média dos últimos 30 dias:
                                <?php echo htmlspecialchars($predictedScore['Average30Days'] ?? 'N/A'); ?>
                            </p>

                            <!-- Média total -->
                            <p class="text-sm text-gray-400">Média total:
                                <?php echo htmlspecialchars($predictedScore['AverageAllTime'] ?? 'N/A'); ?>
                            </p>

                            <!-- Categoria -->
                            <p class="text-sm text-gray-400">Categoria: <?php echo htmlspecialchars($product['Properties']['Category'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Paginação -->
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