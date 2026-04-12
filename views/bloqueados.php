<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto animate-fade-in">
    <header class="mb-12">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-500">
                <i class="fas fa-ban text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black tracking-tight text-white">Bloqueados</h1>
                <p class="text-gray-500 font-medium">Gerencie sua lista de jogadores bloqueados</p>
            </div>
        </div>
    </header>

    <div class="space-y-4">
        <?php if (!empty($blocks)): ?>
            <?php foreach ($blocks as $block): ?>
                <div class="glass-card p-5 rounded-2xl flex items-center justify-between border-white/5">
                    <div class="flex items-center gap-4">
                        <img src="<?= $block['displayPicRaw'] ?? 'img/default_avatar.jpg' ?>" class="w-12 h-12 rounded-full grayscale" alt="Blocked">
                        <div>
                            <h4 class="font-bold text-white"><?= htmlspecialchars($block['gamertag']) ?></h4>
                            <span class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">Bloqueado</span>
                        </div>
                    </div>
                    <button class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-bold text-gray-400 hover:text-white transition-all uppercase tracking-widest">
                        Desbloquear
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="py-20 text-center glass-card rounded-3xl border-dashed opacity-50">
                <i class="fas fa-shield-alt text-5xl mb-6 opacity-20 text-green-500/30"></i>
                <p class="font-bold text-gray-600 uppercase tracking-widest text-sm">Sua lista de bloqueados está limpa</p>
            </div>
        <?php endif; ?>
    </div>
</main>
