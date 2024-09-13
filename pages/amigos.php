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
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT xuid FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['xuid']) {
        $xuid = $user['xuid'];
        $endpoint = "friends";
        $response = openXBLRequest($endpoint);
        if (isset($response['people']) && is_array($response['people'])) {
            $friends = $response['people'];
        } else {
            $friends = [];
        }
    } else {
        echo "Usuário não encontrado ou XUID não disponível.";
        exit;
    }
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4 text-gray-800">Lista de Amigos</h1>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
        <div class="flex items-center border border-gray-300 rounded-full px-4 py-2 space-x-2 shadow-lg w-full md:w-1/2 bg-white">
            <input
                type="text"
                id="filterGamertag"
                placeholder="Buscar por Gamertag..."
                class="outline-none w-full px-4 bg-white text-gray-700 placeholder-gray-500"
                style="border: none; box-shadow: none;" />
            <button id="searchButton" class="outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.415l5.387 5.388a1 1 0 01-1.414 1.414l-5.387-5.387zM8 14a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="flex space-x-4">
            <select id="filterDate" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm w-48">
                <option value="">Data de Amizade</option>
                <option value="oldest">Mais Antigo</option>
                <option value="newest">Mais Recente</option>
            </select>
            <select id="filterGamerscore" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm">
                <option value="">Gamerscore</option>
                <option value="asc">Ordem Crescente</option>
                <option value="desc">Ordem Decrescente</option>
            </select>
        </div>
    </div>
    <?php if (!empty($friends)) : ?>
        <div id="friendsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($friends as $friend) : ?>
                <div class="friend-card bg-white text-gray-800 rounded-lg shadow-md overflow-hidden" data-gamertag="<?php echo htmlspecialchars($friend['gamertag']); ?>" data-added="<?php echo (new DateTime($friend['addedDateTimeUtc']))->format('Y-m-d'); ?>" data-gamerscore="<?php echo $friend['gamerScore']; ?>">
                    <div class="flex">
                        <?php
                        $avatar = !empty($friend['displayPicRaw']) ? $friend['displayPicRaw'] : '../img/default_avatar.jpg';
                        $addedDate = new DateTime($friend['addedDateTimeUtc']);
                        $formattedDate = $addedDate->format('d/m/Y');
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar de <?php echo $friend['displayName']; ?>" class="w-1/3 h-auto object-cover">
                        <div class="p-4 flex-1">
                            <h2 class="gamertag text-2xl font-bold mb-2"><?php echo htmlspecialchars($friend['gamertag']); ?></h2>
                            <p class="added-date text-sm text-gray-500">Amigo desde: <?php echo $formattedDate; ?></p>
                            <p class="gamerscore text-sm text-gray-500 flex items-center">
                                Gamerscore: <?php echo number_format($friend['gamerScore'], 0, ',', '.'); ?>
                                <img src="../img/gs.png" alt="Gamerscore Icon" class="w-4 h-4 ml-2">
                            </p>
                            <p class="text-xs text-gray-400 mt-4"><?php echo $friend['presenceText']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Você não tem amigos adicionados no momento.</p>
    <?php endif; ?>
</div>
<script>
    document.getElementById('filterGamertag').addEventListener('input', filterFriends);
    document.getElementById('filterDate').addEventListener('change', sortFriends);
    document.getElementById('filterGamerscore').addEventListener('change', sortFriends);
    function filterFriends() {
        let filterGamertag = document.getElementById('filterGamertag').value.toLowerCase();
        let friendCards = Array.from(document.querySelectorAll('.friend-card'));
        friendCards.forEach(function(card) {
            let gamertag = card.getAttribute('data-gamertag').toLowerCase();
            if (gamertag.includes(filterGamertag)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
    function sortFriends() {
        let filterDate = document.getElementById('filterDate').value;
        let filterGamerscore = document.getElementById('filterGamerscore').value;
        let friendCards = Array.from(document.querySelectorAll('.friend-card'));
        if (filterDate !== "") {
            friendCards.sort(function(a, b) {
                let dateA = new Date(a.getAttribute('data-added'));
                let dateB = new Date(b.getAttribute('data-added'));
                return (filterDate === "newest") ? dateB - dateA : dateA - dateB;
            });
        }
        if (filterGamerscore !== "") {
            friendCards.sort(function(a, b) {
                let scoreA = parseInt(a.getAttribute('data-gamerscore'));
                let scoreB = parseInt(b.getAttribute('data-gamerscore'));
                return (filterGamerscore === "asc") ? scoreA - scoreB : scoreB - scoreA;
            });
        }
        let friendsList = document.getElementById('friendsList');
        friendCards.forEach(function(card) {
            friendsList.appendChild(card); // Move o card para o fim para reordenar
        });
    }
</script>
<?php include('../includes/footer.php'); ?>