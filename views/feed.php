<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-rss text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Atividade em Tempo Real</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Feed Social</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Acompanhe as conquistas, clipes e novidades da sua rede Xbox.
            </p>
        </div>
    </header>

    <div class="space-y-12 relative">
        <!-- Timeline line -->
        <div class="absolute left-10 top-0 w-px h-full bg-white/5 z-0"></div>

        <?php if (!empty($activities)): ?>
            <?php foreach ($activities as $item): ?>
                <div class="glass-card p-10 rounded-[3rem] border-white/5 relative z-10 hover:border-xbox-green/30 transition-all duration-500">
                    <div class="flex items-start gap-8">
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            <div class="w-20 h-20 rounded-[1.5rem] overflow-hidden border-2 border-xbox-green shadow-2xl">
                                <img src="<?= $item['userImage'] ?? 'img/default_avatar.jpg' ?>" class="w-full h-full object-cover" alt="User">
                            </div>
                            <div class="absolute -top-2 -left-2 w-6 h-6 bg-xbox-green rounded-full border-4 border-xbox-dark flex items-center justify-center">
                                <i class="fas fa-star text-[8px] text-white"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-2xl font-black text-white tracking-tight"><?= htmlspecialchars($item['gamertag'] ?? 'Usuário') ?></h4>
                                    <span class="text-[10px] text-secondary font-black uppercase tracking-widest"><?= date('d M, Y • H:i', strtotime($item['date'] ?? 'now')) ?></span>
                                </div>
                                <span class="px-4 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-black text-secondary uppercase tracking-[0.2em]">
                                    <?= htmlspecialchars($item['activityItemType'] ?? 'Atividade') ?>
                                </span>
                            </div>

                            <p class="text-gray-300 text-lg leading-relaxed mb-8 font-medium">
                                <?= htmlspecialchars($item['descriptionText'] ?? ($item['itemText'] ?? 'Realizou uma nova ação na Live.')) ?>
                            </p>
                            
                            <!-- Activity Image (Achievement, Clip, etc) -->
                            <?php 
                                $activityImg = $item['achievementImage'] ?? ($item['clipThumbnail'] ?? ($item['screenshotThumbnail'] ?? null));
                            ?>
                            <?php if ($activityImg): ?>
                                <div class="rounded-[2.5rem] overflow-hidden border border-white/10 shadow-2xl group cursor-pointer relative">
                                    <img src="<?= $activityImg ?>" 
                                         class="w-full h-auto object-cover max-h-[500px] group-hover:scale-105 transition-transform duration-1000" 
                                         alt="Content">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="fas fa-expand text-white text-3xl opacity-50"></i>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Stats & Interaction -->
                            <div class="mt-10 pt-8 border-t border-white/5 flex flex-wrap gap-8">
                                <button class="flex items-center gap-3 text-secondary hover:text-xbox-green transition-all group/btn">
                                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover/btn:bg-xbox-green/10 transition-colors">
                                        <i class="far fa-heart text-lg"></i>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-widest">Incrível</span>
                                </button>
                                <button class="flex items-center gap-3 text-secondary hover:text-xbox-green transition-all group/btn">
                                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover/btn:bg-xbox-green/10 transition-colors">
                                        <i class="far fa-comment text-lg"></i>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-widest">Comentar</span>
                                </button>
                                <button class="flex items-center gap-3 text-secondary hover:text-xbox-green transition-all group/btn ml-auto">
                                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover/btn:bg-xbox-green/10 transition-colors">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="py-40 text-center glass-card rounded-[4rem] border-dashed opacity-50 relative z-10">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-stream text-4xl opacity-20"></i>
                </div>
                <p class="font-black text-gray-600 uppercase tracking-[0.4em] text-sm">O feed está em silêncio</p>
                <p class="text-xs text-gray-700 font-bold mt-2 uppercase tracking-widest">Adicione mais amigos para ver atividades</p>
            </div>
        <?php endif; ?>
    </div>

    <?= \Anderson\XboxLive\Utils\ViewHelper::renderPagination($currentPage, $totalPages) ?>
</main>
