<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4 text-center md:text-left">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-trophy text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Sua Carreira</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Minhas Conquistas</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Acompanhe sua jornada através dos jogos e desbloqueie o prestígio máximo.
            </p>
        </div>

        <!-- Search Form -->
        <form action="" method="GET" class="relative w-full md:w-80 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-xbox-green transition-colors"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Buscar por jogo..." 
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-sm text-white outline-none focus:border-xbox-green transition-all shadow-inner">
        </form>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
        <?php if (!empty($titles)): ?>
            <?php foreach ($titles as $title): ?>
                <?php 
                    // Robust Data Extraction
                    $ach = $title['achievement'] ?? $title['achievementInfo'] ?? $title['Achievement'] ?? [];
                    $curGS = $ach['currentGamerscore'] ?? $ach['CurrentGamerscore'] ?? 0;
                    $totGS = $ach['totalGamerscore'] ?? $ach['TotalGamerscore'] ?? 1000;
                    $prog  = $ach['progressPercentage'] ?? $ach['ProgressPercentage'] ?? 0;
                    $curAch = $ach['currentAchievements'] ?? $ach['CurrentAchievements'] ?? 0;
                ?>
                <div class="glass-card group p-8 rounded-[2.5rem] hover:border-xbox-green/40 transition-all duration-500 overflow-hidden relative">
                    <!-- Shimmer overlay internally -->
                    <div class="flex items-center gap-6 relative z-10">
                        <div class="w-24 h-24 rounded-[1.5rem] overflow-hidden flex-shrink-0 shadow-2xl border-2 border-white/5 group-hover:border-xbox-green/50 transition-all duration-500">
                            <?php 
                                $gameImg = $title['displayImage'] ?? ($title['imageUri'] ?? 'img/default_game.jpg');
                                if (str_starts_with($gameImg, 'http:')) {
                                    $gameImg = str_replace('http:', 'https:', $gameImg);
                                }
                            ?>
                            <img src="<?= $gameImg ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Game">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-black text-white truncate mb-2 group-hover:text-xbox-green transition-colors"><?= htmlspecialchars($title['name']) ?></h3>
                            <div class="flex items-center gap-2 mb-4">
                                <img src="img/gs.png" class="w-4 h-4" alt="GS">
                                <span class="text-lg font-black text-white"><?= number_format($curGS, 0, ',', '.') ?> <span class="text-gray-600 text-xs">/ <?= number_format($totGS, 0, ',', '.') ?></span></span>
                            </div>
                            <!-- Progress Bar -->
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
                                <div class="h-full bg-gradient-to-r from-xbox-green to-xbox-green-light rounded-full shadow-[0_0_10px_rgba(16,124,16,0.4)]" 
                                     style="width: <?= $prog ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-[9px] font-black text-secondary uppercase tracking-widest"><?= $prog ?>% Completo</span>
                                <span class="text-[9px] font-black text-secondary uppercase tracking-widest"><?= $curAch ?> Troféus</span>
                            </div>
                        </div>
                    </div>
                    <!-- Action Overlay -->
                    <a href="conquistas/jogo?titleId=<?= $title['titleId'] ?>" class="absolute inset-0 z-20"></a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-40 text-center glass-card rounded-[4rem] border-dashed opacity-50">
                <i class="fas fa-ghost text-6xl mb-8 opacity-10"></i>
                <p class="font-black text-gray-600 uppercase tracking-[0.4em] text-sm">Nenhuma conquista registrada</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
