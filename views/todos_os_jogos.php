<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                    <i class="fas fa-list text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest"><?= htmlspecialchars($title) ?></span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white uppercase italic"><?= htmlspecialchars($title) ?></h1>
                <p class="text-secondary text-lg max-w-2xl">Exploração de catálogo com alta performance e dados tipados.</p>
            </div>
            
            <?php if (isset($syncUrl)): ?>
                <a href="<?= $baseUrl . $syncUrl ?>" class="group flex items-center gap-3 px-8 py-4 bg-white/5 hover:bg-xbox-green border border-white/10 hover:border-xbox-green rounded-2xl transition-all duration-500 shadow-xl">
                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-700"></i>
                    <span class="text-xs font-black uppercase tracking-widest">Sincronizar API</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <?php if (isset($_GET['synced'])): ?>
        <div class="mb-12 bg-xbox-green/10 border border-xbox-green/20 text-xbox-green px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-4 animate-fade-in">
            <i class="fas fa-check-circle text-lg"></i>
            <span>Dados atualizados! <?= (int)$_GET['synced'] ?> novos registros processados.</span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-xbox-green/50 transition-all duration-500 shadow-2xl">
                        <img src="<?= $game->boxArt ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                             alt="<?= htmlspecialchars($game->title) ?>">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500 text-center">
                                <span class="badge-gamepass mb-3 inline-block">Official</span>
                                <a href="<?= $baseUrl ?>/jogo?id=<?= $game->productId ?>" class="block w-full py-3 bg-xbox-green text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-xbox-green-light transition-colors shadow-lg">
                                    Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="px-1">
                        <h3 class="text-[11px] font-black text-white truncate group-hover:text-xbox-green transition-colors uppercase tracking-tighter"><?= htmlspecialchars($game->title) ?></h3>
                        <p class="text-[9px] text-secondary font-black uppercase tracking-widest mt-1"><?= htmlspecialchars($game->developer) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-40 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <i class="fas fa-database text-5xl mb-6 opacity-10"></i>
                <p class="font-black text-gray-600 uppercase tracking-widest text-xs">Nenhum registro encontrado para esta página</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
