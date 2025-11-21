<?php
session_start();
include('../includes/header.php');
include('../includes/navbar.php');
require_once '../config/api.php';

// Endpoint para informações da conta
$endpoint = "account";
$response = openXBLRequest($endpoint);
$profileUsers = (is_array($response) && isset($response['profileUsers'][0])) ? $response['profileUsers'][0] : null;

// Endpoint para resumo do jogador (presença)
$summaryEndpoint = "player/summary";
$summaryResponse = openXBLRequest($summaryEndpoint);
$presenceState = $summaryResponse['people'][0]['presenceState'] ?? 'Offline';
$presenceText = $summaryResponse['people'][0]['presenceText'] ?? 'N/A';

// Endpoint para histórico de jogos
$titleHistoryEndpoint = "player/titleHistory";
$titleHistoryResponse = openXBLRequest($titleHistoryEndpoint);
$recentTitles = isset($titleHistoryResponse['titles']) ? array_slice($titleHistoryResponse['titles'], 0, 3) : [];

$gamertag = null;
$settings = $profileUsers['settings'] ?? [];
$gamerpic = '../img/default_avatar.jpg';
$gamerscoreDisplay = '-';
$showGamerscoreIcon = false;
$accountTier = '-';
$reputation = '-';
$bio = '-';
$location = '-';

if ($profileUsers && is_array($settings)) {
    foreach ($settings as $setting) {
        $id = $setting['id'] ?? null;
        $value = $setting['value'] ?? null;

        if ($id === 'Gamertag') {
            $gamertag = $value;
        }

        if ($id === 'GameDisplayPicRaw') {
            $gamerpic = $value;
        }

        if ($id === 'Gamerscore' && is_numeric($value)) {
            $gamerscoreDisplay = $value >= 1000 ? number_format($value, 0, '', '.') : $value;
            $showGamerscoreIcon = true;
        }

        if ($id === 'AccountTier') {
            $accountTier = $value ?: '-';
        }

        if ($id === 'XboxOneRep') {
            $reputation = $value ?: '-';
        }

        if ($id === 'Bio') {
            $bio = $value ?: '-';
        }
        if ($id === 'Location') {
            $location = $value ?: '-';
        }
    }
}
?>
<main class="xbox-content">
    <div class="xbox-page">
        <section class="xbox-hero">
            <h1 class="xbox-hero-title">Bem-vindo, <span class="text-green-400"><?php echo htmlspecialchars($gamertag); ?></span>!</h1>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="xbox-panel">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h2>
                        <span class="xbox-icon"><i class="fas fa-id-card"></i></span>
                        Identidade
                    </h2>
                    <span class="xbox-pill"><i class="fas fa-check"></i> Perfil ativo</span>
                </div>

                <ul class="xbox-stat-list">
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fas fa-medal text-green-400"></i> Gamerscore</span>
                        <span class="xbox-stat-value">
                            <?php echo htmlspecialchars($gamerscoreDisplay); ?>
                            <?php if ($showGamerscoreIcon): ?>
                                <img src="../img/gs.png" alt="Gamerscore Icon" class="inline w-5 h-5">
                            <?php endif; ?>
                        </span>
                    </li>
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fas fa-shield-alt text-green-400"></i> Conta</span>
                        <span class="xbox-stat-value"><?php echo htmlspecialchars($accountTier); ?></span>
                    </li>
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fas fa-thumbs-up text-green-400"></i> Reputação</span>
                        <span class="xbox-stat-value"><?php echo htmlspecialchars($reputation); ?></span>
                    </li>
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fas fa-quote-left text-green-400"></i> Bio</span>
                        <span class="xbox-stat-value"><?php echo htmlspecialchars($bio); ?></span>
                    </li>
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fa-solid fa-location-crosshairs text-green-400"></i> Local</span>
                        <span class="xbox-stat-value"><?php echo htmlspecialchars($location); ?></span>
                    </li>
                </ul>
            </div>

            <div class="xbox-panel">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h2>
                        <span class="xbox-icon"><i class="fa-brands fa-xbox"></i></span>
                        Presença
                    </h2>
                    <span class="xbox-pill <?php echo ($presenceState === 'Online') ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-red-400'; ?>">
                        <i class="fas fa-circle mr-1"></i> <?php echo htmlspecialchars($presenceState); ?>
                    </span>
                </div>
                <ul class="xbox-stat-list">
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fa-solid fa-globe text-green-400"></i> </span>
                        <span class="xbox-stat-value"><?php echo htmlspecialchars($presenceText); ?></span>
                    </li>
                    <li class="xbox-stat-item">
                        <span class="xbox-stat-label"><i class="fas fa-gamepad text-green-400"></i> Jogados recentemente</span>
                        <span class="xbox-stat-value"></span>
                    </li>
                    <?php if (!empty($recentTitles)): ?>
                        <?php foreach ($recentTitles as $title): ?>
                            <li class="xbox-stat-item">
                                <span class="xbox-stat-label"><i class="fas fa-gamepad text-green-400"></i></span>
                                <span class="xbox-stat-value"><?php echo htmlspecialchars($title['name']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="xbox-stat-item">
                            <span class="xbox-stat-label"><i class="fas fa-gamepad text-green-400"></i></span>
                            <span>Nenhum jogo recente encontrado.</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</main>
<?php include('../includes/footer.php'); ?>