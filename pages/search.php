<?php
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    if (isset($_GET['gamertag_search'])) {
        $searchGamertag = trim($_GET['gamertag_search']);
        if (!empty($searchGamertag)) {
            $searchEndpoint = "search/{$searchGamertag}";
            $searchResponse = openXBLRequest($searchEndpoint);
            if (isset($searchResponse['results'][0])) {
                $searchResult = $searchResponse['results'][0];
            } else {
                echo "<p>Gamertag não encontrada.</p>";
            }
        }
    }
?>
<div class="container mx-auto mt-10">
    <?php if (isset($searchResult)): ?>
        <h2 class="text-2xl font-bold">Resultado da Busca</h2>
        <p><strong>Gamertag:</strong> <?php echo $searchResult['gamertag']; ?></p>
        <p><strong>Gamerscore:</strong> <?php echo $searchResult['gamerscore']; ?></p>
        <p><strong>Tier:</strong> <?php echo $searchResult['tier']; ?></p>
        <img src="<?php echo $searchResult['displayPicRaw']; ?>" alt="Imagem do Gamertag" class="w-24 h-24 rounded-full">
    <?php else: ?>
        <p>Por favor, insira um Gamertag válido.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>