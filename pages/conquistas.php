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
        $endpoint = "achievements";
        $response = openXBLRequest($endpoint);
        if (isset($response['titles']) && is_array($response['titles'])) {
            $games = $response['titles'];
        } else {
            $games = [];
        }
    } else {
        echo "Usuário não encontrado ou XUID não disponível.";
        exit;
    }
    function getDeviceIcon($deviceType) {
        switch ($deviceType) {
            case 'XboxSeries':
                return '<img src="../img/xboxseries.png" alt="Xbox Series" class="inline-block w-6 h-6">';
            case 'PC':
                return '<img src="../img/windows.png" alt="PC" class="inline-block w-6 h-6">';
            case 'XboxOne':
                return '<img src="../img/xboxone.png" alt="Xbox One" class="inline-block w-6 h-6">';
            case 'Win32':
                return '<img src="../img/windows.png" alt="PC" class="inline-block w-6 h-6">';
            case 'Mobile':
                return '<img src="../img/windowsphone.webp" alt="Windows Phone" class="inline-block w-6 h-6">';
            case 'Xbox360':
                return '<img src="../img/xbox360.png" alt="Xbox 360" class="inline-block w-6 h-6">';
            default:
                return $deviceType;
        }
    }
    function getBoxArt($game) {
        if (isset($game['images'])) {
            foreach ($game['images'] as $image) {
                if ($image['type'] === 'BoxArt') {
                    return $image['url'];
                }
            }
        }
        return '../img/default_game.jpg';
    }
    $items_per_page = 12;
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl mb-4">Conquistas</h1>
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex items-center border border-gray-300 rounded-full px-4 py-2 space-x-2 shadow-lg w-full md:w-1/2 bg-white">
            <input
                type="text"
                id="searchInput"
                placeholder="Buscar por Nome..."
                class="outline-none w-full px-2 bg-transparent text-gray-700" />
            <button id="searchButton" class="outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.415l5.387 5.388a1 1 0 01-1.414 1.414l-5.387-5.387zM8 14a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="flex space-x-4">
            <select id="filterName" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm">
                <option value="">Nome</option>
                <option value="asc">A-Z</option>
                <option value="desc">Z-A</option>
            </select>
            <select id="filterGamerscore" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm">
                <option value="">Gamerscore</option>
                <option value="asc">Do menor para o maior</option>
                <option value="desc">Do maior para o menor</option>
            </select>
            <select id="filterLastPlayed" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm">
                <option value="">Jogado pela Última Vez</option>
                <option value="recent">Mais Recente</option>
                <option value="oldest">Mais Antigo</option>
            </select>
            <select id="filterPlatform" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm">
                <option value="">Plataformas</option>
                <option value="PC">PC</option>
                <option value="Xbox360">Xbox 360</option>
                <option value="XboxOne">Xbox One</option>
                <option value="XboxSeries">Xbox Series</option>
                <option value="Xbox360">Windows Phone</option>
            </select>
        </div>
    </div>
    <div id="gameList" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Carregar todos os jogos aqui via JavaScript -->
    </div>
    <div class="mt-4">
        <button id="prevPage" class="bg-gray-800 text-white px-4 py-2 rounded">Anterior</button>
        <button id="nextPage" class="bg-gray-800 text-white px-4 py-2 rounded">Próxima</button>
    </div>
</div>
<script>
    const games = <?php echo json_encode($games); ?>;
    const itemsPerPage = <?php echo $items_per_page; ?>;
    let currentPage = 1;
    let filteredGames = games;
    function renderGames() {
        const gameList = document.getElementById('gameList');
        gameList.innerHTML = '';
        const start = (currentPage - 1) * itemsPerPage;
        const end = currentPage * itemsPerPage;
        const gamesToShow = filteredGames.slice(start, end);
        gamesToShow.forEach(game => {
            const gameCard = `
                <div class="bg-gray-800 p-4 rounded-lg shadow-md flex game-card">
                    <div class="relative w-1/3">
                        <img src="${game.images && game.images.find(image => image.type === 'BoxArt') ? game.images.find(image => image.type === 'BoxArt').url : '../img/default_game.jpg'}" alt="Imagem do jogo" class="object-cover rounded-l-md h-full">
                    </div>
                    <div class="w-2/3 p-4">
                        <p class="text-xl font-bold text-white game-name">${game.name}</p>
                        <p class="text-sm text-gray-400 gamerscore">Gamerscore: ${game.achievement.currentGamerscore.toLocaleString()}</p>
                        <p class="text-sm text-gray-400 progress">Progresso: ${game.achievement.progressPercentage}%</p>
                        <p class="text-sm text-gray-400 flex items-center space-x-2 platform">Plataformas: ${game.devices ? game.devices.join(', ') : 'Plataforma não disponível'}</p>
                        <p class="text-sm text-gray-400 last-played">Jogado pela última vez: ${new Date(game.titleHistory.lastTimePlayed).toLocaleDateString()}</p>
                    </div>
                </div>`;
            gameList.innerHTML += gameCard;
        });
        document.getElementById('prevPage').style.display = currentPage === 1 ? 'none' : 'inline';
        document.getElementById('nextPage').style.display = currentPage * itemsPerPage >= filteredGames.length ? 'none' : 'inline';
    }
    function filterGames() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const filterPlatform = document.getElementById('filterPlatform').value;
        filteredGames = games.filter(game => {
            const nameMatch = game.name.toLowerCase().includes(searchInput);
            const platformMatch = filterPlatform === "" || (game.devices && game.devices.includes(filterPlatform));
            return nameMatch && platformMatch;
        });
        currentPage = 1;
        renderGames();
    }
    function sortGames() {
        const filterName = document.getElementById('filterName').value;
        const filterGamerscore = document.getElementById('filterGamerscore').value;
        const filterLastPlayed = document.getElementById('filterLastPlayed').value;
        if (filterName !== "") {
            filteredGames.sort((a, b) => {
                return filterName === 'asc' ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name);
            });
        }
        if (filterGamerscore !== "") {
            filteredGames.sort((a, b) => {
                return filterGamerscore === 'asc' ? a.achievement.currentGamerscore - b.achievement.currentGamerscore : b.achievement.currentGamerscore - a.achievement.currentGamerscore;
            });
        }
        if (filterLastPlayed !== "") {
            filteredGames.sort((a, b) => {
                const lastPlayedA = new Date(a.titleHistory.lastTimePlayed);
                const lastPlayedB = new Date(b.titleHistory.lastTimePlayed);
                return filterLastPlayed === 'recent' ? lastPlayedB - lastPlayedA : lastPlayedA - lastPlayedB;
            });
        }
        currentPage = 1;
        renderGames();
    }
    document.getElementById('searchInput').addEventListener('input', filterGames);
    document.getElementById('filterName').addEventListener('change', sortGames);
    document.getElementById('filterGamerscore').addEventListener('change', sortGames);
    document.getElementById('filterLastPlayed').addEventListener('change', sortGames);
    document.getElementById('filterPlatform').addEventListener('change', filterGames);
    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderGames();
        }
    });
    document.getElementById('nextPage').addEventListener('click', () => {
        if (currentPage * itemsPerPage < filteredGames.length) {
            currentPage++;
            renderGames();
        }
    });
    renderGames();
</script>
<?php include('../includes/footer.php'); ?>