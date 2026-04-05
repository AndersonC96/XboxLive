<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();

use Anderson\XboxLive\Services\AuthService;
use Anderson\XboxLive\Services\OpenXBLService;

if (!AuthService::check()) {
    header('Location: login.php');
    exit();
}

$user = AuthService::user();
$status = $_GET['status'] ?? null;
$profileData = null;

if ($user['xuid']) {
    $api = new OpenXBLService();
    $profile = $api->get("account"); // No OpenXBL, account geralmente retorna os dados da chave
    // Se quisermos dados de outro XUID que não seja o da chave, usamos player/summary ou similar
    $profileData = $profile['profileUsers'][0] ?? null;
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto animate-fade-in">
    <header class="mb-12">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Configurações</span>
        <h1 class="text-4xl font-black tracking-tight text-white mb-4">Seu Perfil</h1>
        <p class="text-gray-500 font-medium">Gerencie sua conta local e seu vínculo com a Xbox Live.</p>
    </header>

    <?php if ($status === 'success'): ?>
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-6 py-4 rounded-2xl mb-8 flex items-center gap-4 animate-scale-in">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-bold">Gamertag vinculada com sucesso!</span>
        </div>
    <?php elseif ($status === 'error'): ?>
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-2xl mb-8 flex items-center gap-4 animate-scale-in">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <span class="font-bold">Não foi possível encontrar ou vincular essa Gamertag.</span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Local Account Info -->
        <div class="glass-card rounded-3xl p-8 border-white/5 shadow-xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <i class="fas fa-user-circle text-xbox-green"></i> 
                Conta Local
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Usuário</label>
                    <p class="text-lg font-bold text-white"><?php echo htmlspecialchars($user['username']); ?></p>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Email</label>
                    <p class="text-lg font-bold text-white"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="pt-4 mt-4 border-t border-white/5 text-gray-600 text-[10px] font-bold uppercase italic">
                    Criado em: 05/04/2026
                </div>
            </div>
        </div>

        <!-- Xbox Link Info -->
        <div class="glass-card rounded-3xl p-8 border-white/5 shadow-xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <i class="fab fa-xbox text-xbox-green"></i> 
                Vínculo Xbox Live
            </h3>

            <?php if ($user['xuid']): ?>
                <div class="flex items-center gap-4 mb-8 p-4 bg-xbox-green/5 border border-xbox-green/20 rounded-2xl">
                    <img src="../img/gs.png" class="w-10 h-10 opacity-50" alt="Xbox">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-xbox-green">XUID Vinculado</p>
                        <p class="text-lg font-mono font-bold text-white"><?php echo htmlspecialchars($user['xuid']); ?></p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Sua conta está vinculada. Os dados da Dashboard agora serão sincronizados com esta Gamertag.
                </p>
            <?php else: ?>
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Você ainda não vinculou uma Gamertag. Vincule para ver suas conquistas, amigos e feeds.
                </p>
            <?php endif; ?>

            <div class="space-y-4">
                <!-- Auto Link -->
                <form action="../actions/link_action.php" method="POST">
                    <input type="hidden" name="action" value="auto">
                    <button type="submit" class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-white font-bold hover:bg-xbox-green hover:border-xbox-green transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-magic"></i>
                        Vincular com a Chave API
                    </button>
                </form>

                <div class="text-center text-[10px] font-black uppercase tracking-widest text-gray-700 py-2">OU</div>

                <!-- Manual Link -->
                <form action="../actions/link_action.php" method="POST" class="space-y-3">
                    <input type="hidden" name="action" value="manual">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" name="gamertag" placeholder="Digite a Gamertag..." required
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 text-white outline-none focus:border-xbox-green transition-all">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-xbox-green/20 border border-xbox-green/30 text-xbox-green font-black uppercase tracking-widest text-xs hover:bg-xbox-green hover:text-white transition-all">
                        Buscar e Vincular
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
