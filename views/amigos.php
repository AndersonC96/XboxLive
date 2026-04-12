<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                    <i class="fas fa-users text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Sincronizado</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Amigos</h1>
                <p class="text-secondary text-lg max-w-2xl">
                    Gerencie sua rede de contatos e veja quem está online agora.
                </p>
            </div>
            <div class="flex items-center gap-4 bg-white/5 p-4 rounded-3xl border border-white/5">
                <div class="text-right">
                    <p class="text-[10px] font-black text-secondary uppercase tracking-widest">Total de Amigos</p>
                    <p class="text-2xl font-black text-white"><?= count($friends) ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-xbox-green flex items-center justify-center shadow-lg shadow-xbox-green/20">
                    <i class="fas fa-user-friends text-white"></i>
                </div>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($friends)): ?>
            <?php foreach ($friends as $friend): ?>
                <div class="glass-card group p-8 rounded-[2rem] hover:border-xbox-green/40 transition-all">
                    <div class="flex items-center gap-6">
                        <div class="relative flex-shrink-0">
                            <img src="<?= $friend['displayPicRaw'] ?? 'img/default_avatar.jpg' ?>" 
                                 class="w-24 h-24 rounded-[1.5rem] border-2 border-white/5 group-hover:border-xbox-green/50 transition-all duration-500 shadow-2xl" 
                                 alt="Avatar">
                            <div class="absolute -bottom-2 -right-2 w-7 h-7 <?= ($friend['presenceState'] === 'Online') ? 'bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.6)]' : 'bg-gray-600' ?> border-4 border-xbox-dark rounded-full"></div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-2xl font-black text-white truncate mb-1"><?= htmlspecialchars($friend['gamertag']) ?></h3>
                            <div class="flex items-center gap-2 mb-4">
                                <img src="img/gs.png" class="w-4 h-4" alt="GS">
                                <span class="text-lg font-black text-xbox-green"><?= number_format($friend['gamerScore'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-white/5 text-secondary border border-white/5 group-hover:border-xbox-green/20 transition-colors">
                                <?= $friend['presenceState'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (isset($friend['presenceText'])): ?>
                        <div class="mt-8 p-5 rounded-2xl bg-black/40 border border-white/5 group-hover:bg-xbox-green/[0.03] transition-colors">
                            <p class="text-xs text-gray-400 font-medium italic leading-relaxed">
                                <i class="fas fa-gamepad mr-2 text-xbox-green/40 text-sm"></i>
                                <?= htmlspecialchars($friend['presenceText']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6 flex gap-2 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                        <button class="flex-1 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-[10px] font-black uppercase tracking-widest text-white transition-all">Perfil</button>
                        <button class="px-4 py-3 rounded-xl bg-xbox-green/10 hover:bg-xbox-green/20 text-xbox-green transition-all">
                            <i class="fas fa-envelope text-xs"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <i class="fas fa-ghost text-6xl mb-8 opacity-10"></i>
                <p class="font-black text-gray-600 uppercase tracking-[0.3em] text-sm">Nenhum amigo encontrado</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
