<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$api = new OpenXBLService();

// Fetch Profile
$profile = $api->get("account");
$profileData = $profile['profileUsers'][0] ?? null;

// Fetch Presence
$presence = $api->get("player/summary");
$presenceState = $presence['people'][0]['presenceState'] ?? 'Offline';
$presenceText = $presence['people'][0]['presenceText'] ?? 'N/A';

// Fetch History
$history = $api->get("player/titleHistory");
$recentTitles = isset($history['titles']) ? array_slice($history['titles'], 0, 4) : [];

// Extract Settings
$stats = [
    'Gamertag'   => 'Usuário',
    'Gamerpic'   => '../img/default_avatar.jpg',
    'Gamerscore' => '0',
    'Tier'       => 'Sliver',
    'Reputation' => 'Good',
    'Bio'        => '-',
    'Location'   => '-'
];

if ($profileData && isset($profileData['settings'])) {
    foreach ($profileData['settings'] as $setting) {
        switch ($setting['id']) {
            case 'Gamertag': $stats['Gamertag'] = $setting['value']; break;
            case 'GameDisplayPicRaw': $stats['Gamerpic'] = $setting['value']; break;
            case 'Gamerscore': $stats['Gamerscore'] = number_format($setting['value'], 0, ',', '.'); break;
            case 'AccountTier': $stats['Tier'] = $setting['value']; break;
            case 'XboxOneRep': $stats['Reputation'] = $setting['value']; break;
            case 'Bio': $stats['Bio'] = $setting['value']; break;
            case 'Location': $stats['Location'] = $setting['value']; break;
        }
    }
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <!-- Hero Section -->
    <header class="mb-12">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4">
            Olá, <span class="text-xbox-green"><?php echo htmlspecialchars($stats['Gamertag']); ?></span>
        </h1>
        <p class="text-gray-500 font-medium max-w-2xl">
            Bem-vindo ao seu centro de comando Xbox. Acompanhe seu progresso, conquistas e atividades em tempo real.
        </p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Identity Card -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-3xl overflow-hidden p-8 flex flex-col items-center text-center">
                <div class="relative mb-6">
                    <img src="<?php echo $stats['Gamerpic']; ?>" alt="Avatar" class="w-28 h-28 rounded-3xl border-4 border-xbox-green shadow-2xl">
                    <div class="absolute -bottom-2 -right-2 bg-xbox-green text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full border-2 border-xbox-dark">
                        Level 1
                    </div>
                </div>
                
                <h2 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($stats['Gamertag']); ?></h2>
                <div class="flex items-center gap-2 mb-8">
                    <img src="../img/gs.png" class="w-4 h-4" alt="GS">
                    <span class="text-xbox-green font-bold"><?php echo htmlspecialchars($stats['Gamerscore']); ?></span>
                </div>

                <div class="w-full space-y-4 text-left">
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase font-bold">Assinatura</span>
                        <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($stats['Tier']); ?></span>
                    </div>
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase font-bold">Reputação</span>
                        <span class="text-sm font-bold text-green-400"><?php echo htmlspecialchars($stats['Reputation']); ?></span>
                    </div>
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                        <span class="block text-xs text-gray-500 uppercase font-bold mb-2">Sobre</span>
                        <p class="text-sm text-gray-400 leading-relaxed italic">"<?php echo htmlspecialchars($stats['Bio']); ?>"</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Presence Panel -->
            <div class="glass-card rounded-3xl p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-xbox-green/10 flex items-center justify-center text-xbox-green">
                            <i class="fas fa-signal text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Status de Atividade</h3>
                            <p class="text-xs text-gray-500 font-medium">Sincronizado com servidores Live</p>
                        </div>
                    </div>
                    <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?php echo ($presenceState === 'Online') ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-500 border border-red-500/20'; ?>">
                        <?php echo htmlspecialchars($presenceState); ?>
                    </span>
                </div>

                <div class="p-6 rounded-2xl bg-xbox-dark/40 border border-white/5 mb-2">
                    <p class="text-sm text-gray-300 font-medium italic">
                        <i class="fas fa-quote-left text-xbox-green/40 mr-2"></i>
                        <?php echo htmlspecialchars($presenceText); ?>
                    </p>
                </div>
            </div>

            <!-- Recent Games -->
            <div>
                <div class="flex items-center justify-between mb-6 px-2">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fas fa-gamepad text-xbox-green"></i> 
                        Jogados Recentemente
                    </h3>
                    <a href="historico.php" class="text-xs font-bold text-gray-500 hover:text-xbox-green transition-colors uppercase tracking-widest">Ver tudo</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (!empty($recentTitles)): ?>
                        <?php foreach ($recentTitles as $title): ?>
                            <div class="glass-card group p-4 rounded-2xl flex items-center gap-4 hover:border-xbox-green/40">
                                <div class="w-16 h-16 rounded-xl overflow-hidden shadow-lg group-hover:scale-105 transition-transform">
                                    <img src="<?php echo $title['imageUri'] ?? '../img/default_game.jpg'; ?>" class="w-full h-full object-cover" alt="Game">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-white truncate mb-1"><?php echo htmlspecialchars($title['name']); ?></h4>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 flex-1 bg-white/5 rounded-full overflow-hidden">
                                            <div class="h-full bg-xbox-green w-[65%]"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-500">65%</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-700 group-hover:text-xbox-green group-hover:translate-x-1 transition-all"></i>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-12 text-center glass-card rounded-2xl border-dashed opacity-50">
                            <i class="fas fa-ghost text-4xl mb-4 opacity-20"></i>
                            <p class="font-bold text-gray-600 uppercase tracking-widest text-xs">Nenhum histórico disponível</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>