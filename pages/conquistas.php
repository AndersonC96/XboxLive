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
                return '<img src="../img/xboxseries.png" alt="Xbox Series" width="62,5" height="62,5">';
            case 'PC':
                return '<img src="../img/windows.png" alt="PC" width="62,5" height="62,5">';
            case 'XboxOne':
                return '<img src="../img/xboxone.png" alt="Xbox One" width="62,5" height="62,5">';
            case 'Win32':
                return '<img src="../img/windows.png" alt="PC" width="62,5" height="62,5">';
            case 'Mobile':
                return '<img src="../img/windowsphone.webp" alt="Windows Phone" width="62,5" height="62,5">';
            case 'Xbox360':
                return '<img src="../img/xbox360.png" alt="Xbox 360" width="62,5" height="62,5">';
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
    <h1 class="text-3xl text-center mb-6">Conquistas</h1>
    <div class="flex flex-col items-center space-y-4">
        <div class="relative w-full max-w-lg">
            <input
                type="text"
                id="searchInput"
                placeholder="Buscar por Nome..."
                class="w-full px-6 py-3 text-gray-700 bg-white rounded-full shadow-lg outline-none focus:ring-2 focus:ring-blue-300" />
            <button class="absolute right-4 top-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a7 7 0 107 7 7 7 0 00-7-7zM21 21l-4.35-4.35" />
                </svg>
            </button>
        </div>
        <div class="flex flex-wrap justify-center space-x-4">
            <select id="filterName" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm w-48">
                <option value="">Nome</option>
                <option value="asc">A-Z</option>
                <option value="desc">Z-A</option>
            </select>
            <select id="filterGamerscore" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm w-48">
                <option value="">Gamerscore</option>
                <option value="asc">Do menor para o maior</option>
                <option value="desc">Do maior para o menor</option>
            </select>
            <select id="filterLastPlayed" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm w-56">
                <option value="">Jogado pela Última Vez</option>
                <option value="recent">Mais Recente</option>
                <option value="oldest">Mais Antigo</option>
            </select>
            <select id="filterPlatform" class="border p-2 rounded-full bg-white text-gray-700 shadow-sm w-48">
                <option value="">Plataformas</option>
                <option value="Mobile">Mobile (Windows Phone)</option>
                <option value="PC">PC (Windows Store)</option>
                <option value="Win32">PC (Outros)</option>
                <option value="Xbox360">Xbox 360</option>
                <option value="XboxOne">Xbox One</option>
                <option value="XboxSeries">Xbox Series</option>
            </select>
        </div>
    </div>
    <div id="gameList" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <!-- Jogos carregados via JavaScript -->
    </div>
    <div class="mt-4 flex justify-center space-x-4">
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
                        <p class="text-sm text-gray-400 gamerscore">Gamerscore: ${game.achievement.currentGamerscore.toLocaleString()} <img src="../img/gs.png" alt="Gamerscore Icon" class="inline-block w-4 h-4"></p>
                        <p class="text-sm text-gray-400 progress">Progresso: ${game.achievement.progressPercentage}%</p>
                        <p class="text-sm text-gray-400 flex items-center space-x-2 platform">Plataformas: ${
                            game.devices ? game.devices.map(device => getDeviceIcon(device)).join(' ') : 'Plataforma não disponível'
                        }</p>
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
    function getDeviceIcon(deviceType) {
        switch (deviceType) {
            case 'XboxSeries':
                return '<img src="../img/xboxseries.png" alt="Xbox Series" width="62,5" height="62,5">';
            case 'PC':
                return '<img src="../img/windows.png" alt="PC" width="62,5" height="62,5">';
            case 'XboxOne':
                return '<img src="../img/xboxone.png" alt="Xbox One" width="62,5" height="62,5">';
            case 'Win32':
                return '<img src="../img/windows.png" alt="PC" width="62,5" height="62,5">';
            case 'Mobile':
                return '<img src="../img/windowsphone.webp" alt="Windows Phone" width="62,5" height="62,5">';
            case 'Xbox360':
                return '<img src="../img/xbox360.png" alt="Xbox 360" width="62,5" height="62,5">';
            default:
                return deviceType;
        }
    }
    renderGames();
</script>
<?php include('../includes/footer.php'); ?>