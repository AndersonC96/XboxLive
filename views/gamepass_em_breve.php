<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400">
                    <i class="fas fa-calendar-alt text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Coming Soon</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Em Breve</h1>
                <p class="text-secondary text-lg max-w-2xl">Prepare o seu HD para as próximas grandes estreias do Game Pass.</p>
            </div>
            <a href="em_breve/sync" class="inline-flex items-center gap-3 px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-purple-600 transition-all group">
                <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Sincronizar Futuros</span>
            </a>
        </div>
    </header>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $product): ?>
                <?php
                    $props = $product['LocalizedProperties'][0] ?? [];
                    $boxArt = 'img/default_game.jpg';
                    foreach ($props['Images'] ?? [] as $img) {
                        if ($img['ImagePurpose'] === 'BoxArt' || $img['ImagePurpose'] === 'Poster') {
                            $boxArt = 'https:' . $img['Uri']; break;
                        }
                    }
                ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-purple-500/50 transition-all duration-500 shadow-2xl">
                        <img src="<?= $boxArt ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <a href="jogo?id=<?= $product['ProductId'] ?>" class="block w-full py-3 bg-purple-600 text-center text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-purple-500 transition-colors shadow-lg">Detalhes</a>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-white truncate px-1"><?= htmlspecialchars($props['ProductTitle'] ?? 'Título') ?></h3>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50"><p class="font-black text-gray-600 uppercase tracking-widest text-xs">Nenhuma estreia confirmada no momento</p></div>
        <?php endif; ?>
    </div>
    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
