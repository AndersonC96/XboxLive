<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-[#ef4444]/10 border border-[#ef4444]/20 text-[#ef4444]">
                    <i class="fas fa-play-circle text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">EA Play Partnership</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white italic">EA PLAY</h1>
                <p class="text-secondary text-lg max-w-2xl">
                    Os melhores títulos da Electronic Arts, agora inclusos na sua assinatura.
                </p>
            </div>
            
            <div class="flex gap-4">
                <a href="ea_play/sync" class="inline-flex items-center gap-3 px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-[#ef4444] transition-all group">
                    <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Sincronizar EA</span>
                </a>
            </div>
        </div>
    </header>

    <?php if (isset($_GET['synced'])): ?>
        <div class="mb-12 bg-green-500/10 border border-green-500/20 text-green-400 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-4">
            <i class="fas fa-check-circle text-lg"></i>
            <span>Sincronização EA Play concluída! <?= (int)$_GET['synced'] ?> novos títulos encontrados.</span>
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
                    $dev = $props['DeveloperName'] ?? 'EA Sports';
                ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-[#ef4444]/50 transition-all duration-500 shadow-2xl">
                        <img src="<?= $boxArt ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                             alt="<?= htmlspecialchars($title) ?>">
                        
                        <!-- EA Badge -->
                        <div class="absolute top-4 left-4 z-10">
                            <span class="bg-white text-[#ef4444] text-[8px] font-black px-2 py-1 rounded uppercase tracking-widest shadow-xl">EA Play</span>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <a href="jogo?id=<?= $product['ProductId'] ?>" class="block w-full py-3 bg-[#ef4444] text-center text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 transition-colors shadow-lg">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="px-1">
                        <h3 class="text-sm font-black text-white truncate group-hover:text-[#ef4444] transition-colors"><?= htmlspecialchars($title) ?></h3>
                        <p class="text-[10px] text-secondary font-black uppercase tracking-widest mt-1"><?= htmlspecialchars($dev) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-play-circle text-4xl opacity-20 text-[#ef4444]"></i>
                </div>
                <p class="font-black text-gray-600 uppercase tracking-[0.3em] text-sm">Catálogo EA Play vazio</p>
                <p class="text-xs text-gray-700 font-bold mt-4 uppercase tracking-widest">Clique em "Sincronizar EA" para carregar os títulos.</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
