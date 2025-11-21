<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "account";
    $response = openXBLRequest($endpoint);
    $profileUsers = (is_array($response) && isset($response['profileUsers'][0])) ? $response['profileUsers'][0] : null;

    $gamertag = null;
    $settings = $profileUsers['settings'] ?? [];
    $gamerpic = '../img/default_avatar.jpg';
    $gamerscoreDisplay = '-';
    $showGamerscoreIcon = false;
    $accountTier = '-';
    $reputation = '-';
    $bio = '-';

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
        }
    }
?>
<main class="xbox-content">
    <div class="xbox-page">
        <section class="xbox-hero">
            <span class="xbox-hero-eyebrow">Painel</span>
            <h1 class="xbox-hero-title">Bem-vindo, <span class="text-green-400"><?php echo htmlspecialchars($gamertag); ?></span>!</h1>
            <p class="xbox-hero-subtitle">Um resumo líquido e iluminado do seu perfil para manter a identidade visual alinhada com a página de login.</p>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="xbox-panel">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h2>
                        <span class="xbox-icon"><i class="fas fa-id-card"></i></span>
                        Identidade Xbox
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
                </ul>
            </div>

            <div class="xbox-panel flex flex-col gap-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="xbox-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Seu perfil</h2>
                        <p class="xbox-hero-subtitle text-sm">Detalhes rápidos em um cartão translúcido para manter a experiência consistente.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <img src="<?php echo htmlspecialchars($gamerpic); ?>" alt="Avatar" class="xbox-avatar">
                    <div class="space-y-2">
                        <span class="xbox-pill"><i class="fas fa-user"></i> <?php echo htmlspecialchars($gamertag ?? 'Jogador'); ?></span>
                        <div class="text-sm text-green-100/80">Personalize seu perfil e conquiste mais com o novo visual.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include('../includes/footer.php'); ?>
