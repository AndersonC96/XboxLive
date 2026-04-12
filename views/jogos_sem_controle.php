<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-yellow-500/10 border border-yellow-500/20 text-yellow-400">
                    <i class="fas fa-hand-pointer text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Touch Optimized</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Sem Controle</h1>
                <p class="text-secondary text-lg max-w-2xl">
                    Jogue em qualquer lugar usando apenas o toque no seu dispositivo móvel.
                </p>
            </div>
            
            <div class="flex gap-4">
                <a href="jogos_sem_controle/sync" class="inline-flex items-center gap-3 px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-yellow-600 transition-all group">
                    <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Sincronizar Cloud</span>
                </a>
            </div>
        </div>
    </header>

    <?php if (isset($_GET['synced'])): ?>
        <div class="mb-12 bg-green-500/10 border border-green-500/20 text-green-400 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-4">
            <i class="fas fa-check-circle text-lg"></i>
            <span>Sincronização Cloud concluída! <?= (int)$_GET['synced'] ?> novos títulos com suporte a toque.</span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $product): ?>
                <?php
                    $props = $product['LocalizedProperties'][0] ?? [];
                    $images = $props['Images'] ?? [];
                    $boxArt = 'img/default_game.jpg';
                    foreach ($images as $img) {
                        if ($img['ImagePurpose'] === 'BoxArt' || $img['ImagePurpose'] === 'Poster') {
                            $boxArt = 'https:' . $img['Uri'];
                            break;
                        }
                    }
                    $title = $props['ProductTitle'] ?? 'Sem Título';
                ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-yellow-500/50 transition-all duration-500 shadow-2xl">
                        <img src="<?= $boxArt ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                             alt="<?= htmlspecialchars($title) ?>">
                        
                        <!-- Touch Badge -->
                        <div class="absolute top-4 left-4 z-10">
                            <span class="bg-yellow-500 text-black text-[8px] font-black px-2 py-1 rounded uppercase tracking-widest shadow-xl flex items-center gap-1">
                                <i class="fas fa-fingerprint text-[10px]"></i> TOUCH
                            </span>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <a href="jogo?id=<?= $product['ProductId'] ?>" class="block w-full py-3 bg-yellow-500 text-center text-black rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-yellow-400 transition-colors shadow-lg">
                                    Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="px-1">
                        <h3 class="text-sm font-black text-white truncate group-hover:text-yellow-400 transition-colors"><?= htmlspecialchars($title) ?></h3>
                        <p class="text-[10px] text-secondary font-black uppercase tracking-widest mt-1">Xbox Cloud Gaming</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-mobile-alt text-4xl opacity-20 text-yellow-500"></i>
                </div>
                <p class="font-black text-gray-600 uppercase tracking-[0.3em] text-sm">Nenhum título Cloud encontrado</p>
                <p class="text-xs text-gray-700 font-bold mt-4 uppercase tracking-widest">Clique em "Sincronizar Cloud" para buscar jogos com suporte a toque.</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
