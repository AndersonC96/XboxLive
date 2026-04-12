<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <!-- Hero Section -->
    <header class="mb-12">
        <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white mb-4">
            Olá, <span class="text-xbox-green"><?= htmlspecialchars($userProfile['gamertag']); ?></span>
        </h1>
        <p class="text-secondary text-lg max-w-2xl">
            Bem-vindo ao seu centro de comando Xbox. Acompanhe seu progresso, conquistas e atividades em tempo real.
        </p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Identity Card (Gamer Card Style) -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-[2rem] overflow-hidden p-8 flex flex-col items-center text-center">
                <!-- Cover Header -->
                <div class="gamer-card-header w-full absolute top-0 left-0 opacity-40" 
                     style="background-image: url('<?= $recentTitles[0]['imageUri'] ?? 'img/logo3.png' ?>'); background-size: cover; background-position: center; filter: blur(4px);">
                </div>

                <div class="relative z-10 mb-6 mt-4">
                    <img src="<?= $userProfile['gamerpic']; ?>" alt="Avatar" class="w-32 h-32 rounded-3xl border-4 border-xbox-green shadow-2xl">
                    <div class="absolute -bottom-2 -right-2 bg-xbox-green text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full border-2 border-xbox-dark shadow-xl">
                        PRO LVL 1
                    </div>
                </div>
                
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-1 tracking-tight"><?= htmlspecialchars($userProfile['gamertag']); ?></h2>
                    <div class="flex items-center justify-center gap-2 mb-8">
                        <img src="img/gs.png" class="w-5 h-5" alt="GS">
                        <span class="text-xbox-green text-xl font-black"><?= htmlspecialchars($userProfile['gamerscore']); ?></span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-left relative z-10">
                    <div class="p-5 bg-white/[0.03] rounded-2xl border border-white/5 flex items-center justify-between group/stat hover:bg-white/[0.06] transition-colors">
                        <span class="text-[10px] text-secondary uppercase font-black tracking-widest">Assinatura</span>
                        <span class="text-sm font-bold text-white"><?= htmlspecialchars($userProfile['tier']); ?></span>
                    </div>
                    <div class="p-5 bg-white/[0.03] rounded-2xl border border-white/5 flex items-center justify-between group/stat hover:bg-white/[0.06] transition-colors">
                        <span class="text-[10px] text-secondary uppercase font-black tracking-widest">Reputação</span>
                        <span class="text-sm font-bold text-green-400"><?= htmlspecialchars($userProfile['reputation']); ?></span>
                    </div>
                    <div class="p-5 bg-white/[0.03] rounded-2xl border border-white/5 hover:bg-white/[0.06] transition-colors">
                        <span class="block text-[10px] text-secondary uppercase font-black tracking-widest mb-2">Bio</span>
                        <p class="text-sm text-gray-400 leading-relaxed italic font-medium">"<?= htmlspecialchars($userProfile['bio']); ?>"</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Presence Panel -->
            <div class="glass-card rounded-[2rem] p-10 border-l-4 border-l-xbox-green">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-xbox-green/10 flex items-center justify-center text-xbox-green shadow-inner">
                            <i class="fas fa-satellite-dish text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black">Status de Atividade</h3>
                            <p class="text-xs text-secondary">Sincronização em tempo real</p>
                        </div>
                    </div>
                    <span class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] <?= ($presenceState === 'Online') ? 'bg-green-500/10 text-green-400 border border-green-500/20 shadow-[0_0_15px_rgba(34,197,94,0.1)]' : 'bg-red-500/10 text-red-500 border border-red-500/20'; ?>">
                        <?= htmlspecialchars($presenceState); ?>
                    </span>
                </div>

                <div class="p-8 rounded-3xl bg-black/40 border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-1 h-full bg-xbox-green opacity-30"></div>
                    <p class="text-lg text-gray-200 font-bold leading-relaxed relative z-10">
                        <i class="fas fa-quote-left text-xbox-green/40 mr-3 text-2xl align-top"></i>
                        <?= htmlspecialchars($presenceText); ?>
                    </p>
                </div>
            </div>

            <!-- Recent Games -->
            <div>
                <div class="flex items-center justify-between mb-8 px-4">
                    <h3 class="text-2xl font-black flex items-center gap-4">
                        <span class="w-2 h-8 bg-xbox-green rounded-full"></span>
                        Recentemente
                    </h3>
                    <a href="historico" class="group flex items-center gap-2 text-xs font-black text-secondary hover:text-xbox-green transition-all uppercase tracking-widest">
                        Ver histórico
                        <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (!empty($recentTitles)): ?>
                        <?php foreach ($recentTitles as $title): ?>
                            <a href="jogo?id=<?= $title['titleId']; ?>" class="glass-card group p-5 rounded-[2rem] flex items-center gap-5">
                                <div class="w-24 h-24 rounded-2xl overflow-hidden shadow-2xl flex-shrink-0 group-hover:scale-105 transition-transform duration-500">
                                    <img src="<?= $title['imageUri'] ?? 'img/default_game.jpg'; ?>" class="w-full h-full object-cover" alt="Game">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-black text-white truncate mb-2"><?= htmlspecialchars($title['name']); ?></h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[10px] font-black text-secondary uppercase tracking-widest">Progresso</span>
                                            <span class="text-[10px] font-black text-xbox-green"><?= $title['achievement']['progressPercentage'] ?? 0; ?>%</span>
                                        </div>
                                        <div class="h-2 bg-white/[0.03] rounded-full overflow-hidden border border-white/5">
                                            <div class="h-full bg-gradient-to-r from-xbox-green to-xbox-green-light rounded-full shadow-[0_0_10px_rgba(16,124,16,0.5)] transition-all duration-1000" 
                                                 style="width: <?= $title['achievement']['progressPercentage'] ?? 0; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-20 text-center glass-card rounded-[2rem] border-dashed opacity-30">
                            <i class="fas fa-gamepad text-5xl mb-6 opacity-10"></i>
                            <p class="font-bold text-gray-700 uppercase tracking-widest text-xs">Aguardando nova atividade</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
