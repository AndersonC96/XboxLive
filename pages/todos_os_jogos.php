<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require '../config/db.php';
    require_once '../config/api.php';
    $games = [];
    function getGamepassGames() {
        $endpoint = "gamepass/all";
        $response = openXBLRequest($endpoint);
        return isset($response) && is_array($response) ? $response : [];
    }
    $games = getGamepassGames();
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Todos os Jogos no Game Pass</h1>
    <?php if (!empty($games)) : ?>
        <ul>
            <?php foreach ($games as $game) : ?>
                <li class="mb-4">
                    <div class="flex items-center space-x-4">
                        <p class="text-xl">ID do Jogo: <?php echo htmlspecialchars($game['id']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Nenhum jogo encontrado no Game Pass.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>