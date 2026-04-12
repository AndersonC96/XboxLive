<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-search text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Global Search</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Busca de Jogadores</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Resultados para: <span class="text-white italic">"<?= htmlspecialchars($query) ?>"</span>
            </p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $player): ?>
                <div class="glass-card group p-8 rounded-[2.5rem] hover:border-xbox-green/40 transition-all duration-500 shadow-2xl">
                    <div class="flex items-center gap-8">
                        <div class="relative flex-shrink-0">
                            <img src="<?= $player['displayPicRaw'] ?? 'img/default_avatar.jpg' ?>" 
                                 class="w-24 h-24 rounded-[1.5rem] border-2 border-white/5 group-hover:border-xbox-green/50 transition-all duration-500 shadow-xl" 
                                 alt="Avatar">
                            <div class="absolute -bottom-2 -right-2 w-7 h-7 bg-gray-600 border-4 border-xbox-dark rounded-full"></div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-2xl font-black text-white truncate mb-1 italic uppercase tracking-tighter"><?= htmlspecialchars($player['gamertag']) ?></h3>
                            <div class="flex items-center gap-2 mb-4">
                                <img src="img/gs.png" class="w-4 h-4" alt="GS">
                                <span class="text-lg font-black text-xbox-green"><?= number_format($player['gamerScore'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <button class="px-6 py-2.5 rounded-xl bg-xbox-green text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-xbox-green/20 hover:scale-105 transition-all">Ver Perfil</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-40 text-center glass-card rounded-[4rem] border-dashed opacity-50">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-search text-4xl opacity-20"></i>
                </div>
                <p class="font-black text-gray-600 uppercase tracking-[0.4em] text-sm">Nenhum jogador encontrado</p>
                <p class="text-xs text-gray-700 font-bold mt-2 uppercase tracking-widest">Tente buscar por um Gamertag exato ou parcial</p>
            </div>
        <?php endif; ?>
    </div>
</main>
