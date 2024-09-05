<?php
    session_start();
    require '../config/db.php';
    require '../config/api.php';
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT xuid FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['xuid']) {
        $xuid = $user['xuid'];
        $endpoint = "account";
        $response = openXBLRequest($endpoint);
        if (isset($response['profileUsers'][0]['settings'])) {
            $settings = $response['profileUsers'][0]['settings'];
            foreach ($settings as $setting) {
                if ($setting['id'] === 'GameDisplayPicRaw') {
                    $gamerpic = $setting['value'];
                }
                if ($setting['id'] === 'Gamertag') {
                    $gamertag = $setting['value'];
                }
            }
        } else {
            $gamerpic = '../img/default_avatar.jpg';
            $gamertag = 'Usuário';
        }
    } else {
        $gamerpic = '../img/default_avatar.jpg';
        $gamertag = 'Usuário';
    }
    $searchResult = null;
    if (isset($_GET['gamertag_search'])) {
        $searchGamertag = trim($_GET['gamertag_search']);
        if (!empty($searchGamertag)) {
            $searchEndpoint = "search/{$searchGamertag}";
            $searchResponse = openXBLRequest($searchEndpoint);
            if (isset($searchResponse['results'][0])) {
                $searchResult = $searchResponse['results'][0];
            }
        }
    }
?>
<nav class="bg-gray-800 p-4">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="dashboard.php">
                <img src="../img/logo2.png" alt="Xbox Logo" class="w-10 h-10">
            </a>
            <a href="dashboard.php" class="text-white">Conta</a>
            <a href="#" class="text-white">Conquistas</a>
            <a href="#" class="text-white">Atividade</a>
            <a href="#" class="text-white">Clubes</a>
            <a href="#" class="text-white">Conversas</a>
            <a href="#" class="text-white">DVR</a>
            <a href="#" class="text-white">Amigos</a>
            <a href="#" class="text-white">Gamepass</a>
            <a href="#" class="text-white">Loja</a>
            <a href="#" class="text-white">Jogador</a>
            <a href="#" class="text-white">Sessão</a>
            <a href="#" class="text-white">Grupo</a>
        </div>
        <div class="flex items-center space-x-4">
            <form action="../pages/search.php" method="GET" class="flex">
                <input type="text" name="gamertag_search" placeholder="Buscar Gamertag" class="px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400" required>
                <button type="submit" class="ml-2 bg-green-500 text-white px-4 py-2 rounded-lg">Buscar</button>
            </form>
            <img src="<?php echo $gamerpic; ?>" alt="Profile" class="w-10 h-10 rounded-full">
            <a href="logout.php" class="text-white">Sair</a>
        </div>
    </div>
</nav>
<?php if ($searchResult): ?>
<div class="container mx-auto mt-6 bg-gray-800 text-white p-4 rounded-md shadow-md">
    <h2 class="text-2xl font-bold">Resultado da Busca</h2>
    <p><strong>Gamertag:</strong> <?php echo $searchResult['gamertag']; ?></p>
    <p><strong>Gamerscore:</strong> <?php echo $searchResult['gamerscore']; ?></p>
    <p><strong>Tier:</strong> <?php echo $searchResult['tier']; ?></p>
    <img src="<?php echo $searchResult['displayPicRaw']; ?>" alt="Imagem do Gamertag" class="w-24 h-24 rounded-full">
</div>
<?php endif; ?>