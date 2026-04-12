<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                    <i class="fas fa-list text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Catálogo Game Pass</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Todos os Jogos</h1>
                <p class="text-secondary text-lg max-w-2xl">
                    Explore a biblioteca completa sincronizada com o banco de dados.
                </p>
            </div>
            
            <div class="flex gap-4">
                <a href="todos_os_jogos/sync" class="inline-flex items-center gap-3 px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-xbox-green transition-all group">
                    <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Sincronizar API</span>
                </a>
            </div>
        </div>
    </header>

    <?php if (isset($_GET['synced'])): ?>
        <div class="mb-12 bg-xbox-green/10 border border-xbox-green/20 text-xbox-green px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-4">
            <i class="fas fa-check-circle text-lg"></i>
            <span>Sincronização concluída! <?= (int)$_GET['synced'] ?> novos títulos adicionados.</span>
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
                    $dev = $props['DeveloperName'] ?? 'Xbox Game Studios';
                ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-xbox-green/50 transition-all duration-500 shadow-2xl">
                        <img src="<?= $boxArt ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                             alt="<?= htmlspecialchars($title) ?>">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <span class="badge-gamepass mb-3 inline-block">Game Pass</span>
                                <a href="jogo?id=<?= $product['ProductId'] ?>" class="block w-full py-3 bg-xbox-green text-center text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-xbox-green-light transition-colors shadow-lg">
                                    Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="px-1">
                        <h3 class="text-sm font-black text-white truncate group-hover:text-xbox-green transition-colors"><?= htmlspecialchars($title) ?></h3>
                        <p class="text-[10px] text-secondary font-black uppercase tracking-widest mt-1"><?= htmlspecialchars($dev) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-database text-4xl opacity-20"></i>
                </div>
                <p class="font-black text-gray-600 uppercase tracking-[0.3em] text-sm">Banco de dados vazio</p>
                <p class="text-xs text-gray-700 font-bold mt-4 uppercase tracking-widest">Clique no botão "Sincronizar API" para carregar os jogos.</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
