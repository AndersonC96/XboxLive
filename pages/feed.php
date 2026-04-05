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
$response = $api->get("activity/feed");
$activityItems = $response['activityItems'] ?? [];

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-12">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-xbox-green mb-3 block">Comunidade</span>
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4">Feed de Atividades</h1>
        <p class="text-gray-500 font-medium">Fique por dentro do que seus amigos estão jogando e conquistando.</p>
    </header>

    <?php if (!empty($activityItems)) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($activityItems as $item) : ?>
                <?php
                    $author = $item['authorInfo'] ?? [];
                    $avatar = $author['imageUrl'] ?? '../img/default_avatar.jpg';
                    $gamertag = $author['modernGamertag'] ?? 'Usuário';
                    $description = $item['description'] ?? 'Atividade';
                    $itemText = $item['itemText'] ?? '';
                    $dateRaw = $item['date'] ?? null;
                    $date = $dateRaw ? date('d/m/Y H:i', strtotime($dateRaw)) : 'Agora';
                ?>
                <article class="glass-card rounded-3xl p-8 hover:border-xbox-green/30 transition-all">
                    <div class="flex items-start gap-5 mb-6">
                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="w-14 h-14 rounded-2xl border-2 border-white/5 object-cover">
                        <div>
                            <h3 class="font-bold text-lg leading-tight text-white"><?php echo htmlspecialchars($gamertag); ?></h3>
                            <p class="text-xs text-xbox-green font-bold uppercase tracking-widest mt-1"><?php echo htmlspecialchars($description); ?></p>
                            <p class="text-[10px] text-gray-600 font-bold uppercase mt-1"><?php echo $date; ?></p>
                        </div>
                    </div>

                    <?php if ($itemText): ?>
                        <div class="p-5 rounded-2xl bg-white/5 border border-white/5 italic text-gray-300 text-sm leading-relaxed mb-6">
                            "<?php echo htmlspecialchars($itemText); ?>"
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center gap-6 pt-6 border-t border-white/5">
                        <button class="flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-xbox-green transition-colors">
                            <i class="far fa-heart"></i> Curtir
                        </button>
                        <button class="flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-xbox-green transition-colors">
                            <i class="far fa-comment"></i> Comentar
                        </button>
                        <button class="ml-auto text-xs font-bold text-gray-500 hover:text-white transition-colors">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="py-32 text-center glass-card rounded-3xl border-dashed">
            <i class="fas fa-rss text-6xl text-gray-800 mb-6"></i>
            <p class="text-xl font-bold text-gray-600 uppercase tracking-widest">Nenhuma atividade no feed</p>
        </div>
    <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>