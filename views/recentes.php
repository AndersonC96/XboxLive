<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-history text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Sessões Recentes</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Jogadores Recentes</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Pessoas que cruzaram seu caminho nas últimas partidas online.
            </p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($players)): ?>
            <?php foreach ($players as $player): ?>
                <div class="glass-card group p-8 rounded-[2rem] hover:border-xbox-green/40 transition-all">
                    <div class="flex items-center gap-6">
                        <img src="<?= $player['displayPicRaw'] ?>" 
                             onerror="this.src='img/default_avatar.jpg'"
                             class="w-20 h-20 rounded-[1.5rem] border-2 border-white/5 group-hover:border-xbox-green/50 transition-all duration-500 shadow-2xl" 
                             alt="Avatar">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-black text-white truncate mb-1"><?= htmlspecialchars($player['gamertag']) ?></h3>
                            <?php if ($player['isCodHq']): ?>
                                <span class="inline-block px-2 py-0.5 rounded bg-xbox-green text-[9px] font-black text-black uppercase tracking-tighter mb-1">COD LAUNCHER</span>
                            <?php endif; ?>
                            <p class="text-[10px] font-bold text-secondary uppercase tracking-widest line-clamp-1"><?= htmlspecialchars($player['encounterGame']) ?></p>
                            <p class="text-[9px] font-medium text-gray-500 italic"><?= htmlspecialchars($player['encounterText']) ?></p>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <a href="https://social.xbox.com/p/<?= urlencode($player['gamertag']) ?>" target="_blank" class="flex-1 py-3 text-center rounded-xl bg-white/5 hover:bg-white/10 text-[10px] font-black uppercase tracking-widest text-white transition-all">Perfil Social</a>
                        <button class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white transition-all">
                             <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <i class="fas fa-user-friends text-6xl mb-8 opacity-10"></i>
                <p class="font-black text-gray-600 uppercase tracking-[0.3em] text-sm">Nenhum jogador recente</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
