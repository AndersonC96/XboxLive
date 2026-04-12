<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                    <i class="fas fa-camera text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Sua Galeria</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Capturas</h1>
                <p class="text-secondary text-lg max-w-2xl">
                    Reviva seus momentos mais épicos através de fotos e clipes.
                </p>
            </div>
        </div>
    </header>

    <div class="space-y-20">
        <!-- Screenshots -->
        <section>
            <h3 class="text-2xl font-black mb-10 flex items-center gap-4 italic tracking-tighter">
                <span class="w-2 h-8 bg-xbox-green rounded-full"></span>
                SCREENSHOTS
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($screenshots)): ?>
                    <?php foreach ($screenshots as $ss): ?>
                        <?php 
                            $ssUri = $ss['screenshotUris'][0]['uri'] ?? ($ss['uri'] ?? '');
                        ?>
                        <div class="glass-card rounded-[2.5rem] overflow-hidden border-white/5 group relative cursor-pointer shadow-2xl">
                            <img src="<?= $ssUri ?>" class="w-full h-auto aspect-video object-cover group-hover:scale-105 transition-transform duration-1000">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-8 flex flex-col justify-end">
                                <p class="text-white font-black italic uppercase text-lg"><?= htmlspecialchars($ss['titleName'] ?? 'Xbox Gameplay') ?></p>
                                <p class="text-secondary text-[10px] font-black uppercase tracking-widest mt-1"><?= date('d/m/Y', strtotime($ss['dateTaken'] ?? 'now')) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center glass-card rounded-[3rem] border-dashed opacity-30">
                        <p class="font-black text-gray-600 uppercase tracking-widest text-xs">Nenhuma foto encontrada</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Game Clips -->
        <section>
            <h3 class="text-2xl font-black mb-10 flex items-center gap-4 italic tracking-tighter">
                <span class="w-2 h-8 bg-xbox-green rounded-full"></span>
                GAME CLIPS
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php if (!empty($clips)): ?>
                    <?php foreach ($clips as $clip): ?>
                        <div class="glass-card rounded-[2.5rem] overflow-hidden border-white/5 group relative cursor-pointer shadow-2xl">
                            <div class="aspect-video relative">
                                <img src="<?= $clip['thumbnails'][0]['uri'] ?? ($clip['uri'] ?? '') ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-20 h-20 rounded-full bg-xbox-green/80 flex items-center justify-center text-white text-3xl shadow-[0_0_30px_rgba(16,124,16,0.6)] group-hover:scale-110 transition-transform duration-500">
                                        <i class="fas fa-play ml-1"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8">
                                <h4 class="text-xl font-black text-white italic uppercase tracking-tighter"><?= htmlspecialchars($clip['titleName'] ?? 'Xbox Video') ?></h4>
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-[10px] font-black text-secondary uppercase tracking-widest"><?= date('d/m/Y', strtotime($clip['dateRecorded'] ?? 'now')) ?></span>
                                    <span class="text-[10px] font-black text-xbox-green uppercase tracking-widest italic"><?= gmdate("i:s", $clip['durationInSeconds'] ?? 0) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center glass-card rounded-[3rem] border-dashed opacity-30">
                        <p class="font-black text-gray-600 uppercase tracking-widest text-xs">Nenhum clipe encontrado</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
