<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "activity/feed";
    $response = openXBLRequest($endpoint);
    $activityItems = $response['activityItems'] ?? [];
    $items_per_page = 5;
    $total_items = count($activityItems);
    $total_pages = ceil($total_items / $items_per_page);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    $current_page_items = array_slice($activityItems, $offset, $items_per_page);
?>
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6 text-center text-green-400">Feed de Atividades</h1>
    <?php if (!empty($current_page_items)): ?>
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($current_page_items as $item): ?>
                <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:bg-gray-700 transition duration-200">
                    <div class="flex items-center mb-4">
                        <img src="<?php echo $item['authorInfo']['imageUrl']; ?>" alt="Avatar" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <p class="text-lg font-bold text-green-400"><?php echo $item['authorInfo']['modernGamertag']; ?></p>
                            <p class="text-sm text-gray-400"><?php echo $item['authorInfo']['secondName'] ?? 'Nome não disponível'; ?></p>
                        </div>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-lg">
                        <p class="text-white mb-2"><strong><?php echo $item['description']; ?></strong></p>
                        <p class="text-gray-400"><?php echo $item['itemText'] ?? 'Texto não disponível'; ?></p>
                        <p class="text-gray-500 text-sm mt-1">
                            Compartilhado em: <?php echo $item['timeline']['timelineName'] ?? 'Timeline não disponível'; ?>
                        </p>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-gray-500 text-xs">
                                Publicado em: <?php echo isset($item['date']) ? date('d/m/Y H:i', strtotime($item['date'])) : 'Data não disponível'; ?>
                            </p>
                        </div>
                        <div class="flex justify-between mt-2">
                            <p class="text-sm text-gray-400">
                                Comentários: <?php echo isset($item['numComments']) ? $item['numComments'] : 'Sem comentários'; ?>
                            </p>
                            <p class="text-sm text-gray-400">
                                Likes: <?php echo $item['hasLiked'] ? '✔️' : '❌'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between mt-6">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Anterior</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 ml-auto">Próxima</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-400 text-center">Nenhuma atividade disponível no momento.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>