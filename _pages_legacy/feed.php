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
$user = AuthService::user();
$statusSuccess = false;
$statusError = null;

// Lógica de Postagem de Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_message'])) {
    $message = trim($_POST['status_message']);
    if (!empty($message)) {
        $result = $api->postToActivityFeed($message);
        if ($result) {
            $statusSuccess = true;
        } else {
            $statusError = "Não foi possível postar o status agora.";
        }
    }
}

// Busca de Atividades
$response = $api->getActivityFeed();
$activityItems = $response['activityItems'] ?? [];

// Paginação Simples
$itemsPerPage = 8;
$totalItems = count($activityItems);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

$offset = ($currentPage - 1) * $itemsPerPage;
$items = array_slice($activityItems, $offset, $itemsPerPage);

// Helper para Tempo Relativo
function time_elapsed_string($datetime, $full = false) {
    if (!$datetime) return 'Agora';
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? ($k === 'm' ? 'es' : 's') : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'há ' . implode(', ', $string) : 'agora';
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto animate-fade-in space-y-12">
    <!-- Header Social -->
    <header class="text-center">
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-xbox-green mb-3 block text-shadow-sm">Comunidade</span>
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white italic drop-shadow-lg uppercase">Social Hub</h1>
        <p class="text-gray-500 font-medium mt-2">Acompanhe a pulsação da sua rede Xbox Live em tempo real.</p>
    </header>

    <!-- Composer: O que você está jogando? -->
    <section class="glass-card rounded-[2.5rem] p-8 border-white/5 bg-gradient-to-br from-white/[0.03] to-transparent shadow-2xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-xbox-green/5 blur-3xl rounded-full"></div>
        
        <form action="" method="POST" class="relative z-10">
            <div class="flex gap-4 mb-6">
                <img src="<?php echo $user['avatar'] ?? '../img/default_avatar.jpg'; ?>" class="w-12 h-12 rounded-2xl border border-white/10 shadow-lg object-cover">
                <div class="flex-1">
                    <textarea name="status_message" rows="2" placeholder="O que você está jogando hoje?" 
                        class="w-full bg-transparent border-none text-white placeholder-gray-600 focus:ring-0 text-lg font-medium resize-none" required></textarea>
                </div>
            </div>
            
            <div class="flex items-center justify-between pt-4 border-t border-white/5">
                <div class="flex gap-4 text-gray-500 text-sm">
                    <button type="button" class="hover:text-xbox-green transition-colors"><i class="fas fa-camera"></i></button>
                    <button type="button" class="hover:text-xbox-green transition-colors"><i class="fas fa-video"></i></button>
                    <button type="button" class="hover:text-xbox-green transition-colors"><i class="fas fa-gamepad"></i></button>
                </div>
                <button type="submit" class="px-8 py-3 rounded-2xl bg-xbox-green text-white font-black uppercase tracking-widest text-[10px] hover:scale-105 hover:shadow-[0_10px_30px_rgba(16,124,16,0.4)] transition-all">
                    COMPARTILHAR <i class="fas fa-paper-plane ml-2"></i>
                </button>
            </div>
        </form>

        <?php if ($statusSuccess): ?>
            <div class="mt-4 p-3 rounded-xl bg-xbox-green/20 border border-xbox-green/30 text-xbox-green text-[10px] font-black uppercase tracking-widest text-center animate-bounce">
                Status postado com sucesso!
            </div>
        <?php endif; ?>
    </section>

    <!-- Feed Timeline -->
    <div class="space-y-8 relative">
        <!-- Vertical Line -->
        <div class="absolute left-1/2 -translate-x-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-white/10 via-white/5 to-transparent hidden md:block"></div>

        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <?php
                    $author = $item['authorInfo'] ?? [];
                    $avatar = $author['imageUrl'] ?? '../img/default_avatar.jpg';
                    $gt = $author['modernGamertag'] ?? 'Usuário';
                    $desc = $item['description'] ?? 'Atividade';
                    $text = $item['itemText'] ?? '';
                    $date = time_elapsed_string($item['date'] ?? null);
                    
                    // Detecção de Tipo
                    $isAchievement = stripos($desc, 'conquista') !== false || stripos($desc, 'achievement') !== false;
                    $isClip = stripos($desc, 'clipe') !== false || stripos($desc, 'clip') !== false;
                ?>
                <article class="glass-card rounded-[2rem] p-8 border-white/5 hover:border-xbox-green/30 transition-all relative z-10 group">
                    <div class="flex items-start gap-6">
                        <div class="relative shrink-0">
                            <img src="<?php echo htmlspecialchars($avatar); ?>" class="w-14 h-14 rounded-[1.2rem] border-2 border-white/5 shadow-xl object-cover">
                            <?php if ($isAchievement): ?>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-yellow-500 text-white flex items-center justify-center text-[10px] shadow-lg border border-xbox-dark">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            <?php elseif ($isClip): ?>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-xbox-green text-white flex items-center justify-center text-[10px] shadow-lg border border-xbox-dark">
                                    <i class="fas fa-play"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-bold text-white text-lg tracking-tight"><?php echo htmlspecialchars($gt); ?></h3>
                                <span class="text-[9px] font-black uppercase text-gray-600 tracking-widest italic"><?php echo $date; ?></span>
                            </div>
                            <p class="text-[10px] font-black uppercase text-xbox-green tracking-[0.2em] mb-4 opacity-80"><?php echo htmlspecialchars($desc); ?></p>
                            
                            <?php if ($text): ?>
                                <div class="text-gray-300 font-medium leading-relaxed mb-6 italic opacity-90 border-l-2 border-xbox-green/20 pl-4 py-1">
                                    "<?php echo htmlspecialchars($text); ?>"
                                </div>
                            <?php endif; ?>

                            <!-- Social Actions -->
                            <div class="flex items-center gap-8 pt-4 border-t border-white/5">
                                <button class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-xbox-green transition-colors">
                                    <i class="far fa-heart text-sm"></i> Curtir
                                </button>
                                <button class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-xbox-green transition-colors">
                                    <i class="far fa-comment text-sm"></i> Responder
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <!-- Pagination -->
            <div class="pt-10">
                <?php echo \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages, 'feed.php'); ?>
            </div>

        <?php else: ?>
            <div class="py-32 text-center glass-card rounded-[3rem] border-white/5 opacity-80 relative overflow-hidden">
                <div class="absolute inset-0 opacity-5 pointer-events-none flex items-center justify-center">
                    <i class="fas fa-rss text-[20rem] rotate-12"></i>
                </div>
                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-[2rem] bg-white/5 flex items-center justify-center text-gray-600 mx-auto mb-6 border border-white/10 shadow-inner">
                        <i class="fas fa-ghost text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2 italic">A REDE ESTÁ CALMA...</h3>
                    <p class="text-gray-600 font-medium max-w-xs mx-auto text-sm leading-relaxed">Não há atividades recentes no seu círculo social. Comece postando algo novo!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include('../includes/footer.php'); ?>