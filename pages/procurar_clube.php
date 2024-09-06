<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
    $clubs = [];
    if ($searchQuery) {
        $endpoint = "clubs/find?q=" . urlencode($searchQuery);
        $response = openXBLRequest($endpoint);
        if (isset($response['results'])) {
            $clubs = $response['results'];
        }
    }
?>
<div class="container mx-auto mt-10">
    <form method="GET" action="procurar_clube.php" class="mb-6">
        <input type="text" name="q" placeholder="Digite o nome do clube" class="px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400 w-full" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
        <button type="submit" class="mt-2 bg-green-500 text-white px-4 py-2 rounded-lg">Buscar</button>
    </form>
    <?php if ($searchQuery && empty($clubs)): ?>
        <p class="text-white">Nenhum clube encontrado para "<?php echo htmlspecialchars($searchQuery); ?>"</p>
    <?php elseif (!empty($clubs)): ?>
        <ul class="bg-gray-800 p-4 rounded-lg shadow-md">
            <?php foreach ($clubs as $club): ?>
                <li class="border-b border-gray-700 py-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo $club['result']['displayImageUrl']; ?>" alt="Imagem do clube" class="w-16 h-16 rounded-lg">
                        <div>
                            <h2 class="text-xl font-bold text-white"><?php echo $club['result']['name']; ?></h2>
                            <p class="text-gray-400"><?php echo $club['result']['description'] ?? 'Sem descrição'; ?></p>
                            <p class="text-gray-400">Idiomas: <?php echo implode(', ', $club['result']['languages']); ?></p>
                            <p class="text-gray-400">Tags: <?php echo implode(', ', $club['result']['tags']); ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>