<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-store text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Microsoft Store</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Loja Xbox</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Explore o melhor conteúdo digital para consoles Xbox e PC Windows.
            </p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-20">
        <a href="promocao" class="glass-card p-10 rounded-[2.5rem] bg-gradient-to-br from-red-500/10 to-transparent border-red-500/20 group hover:border-red-500/50 transition-all">
            <i class="fas fa-tags text-3xl text-red-500 mb-6"></i>
            <h3 class="text-2xl font-black text-white italic uppercase mb-2">Ofertas</h3>
            <p class="text-secondary text-sm font-medium">Descontos imperdíveis de até 90%.</p>
        </a>
        <a href="mais_jogados" class="glass-card p-10 rounded-[2.5rem] bg-gradient-to-br from-xbox-green/10 to-transparent border-xbox-green/20 group hover:border-xbox-green/50 transition-all">
            <i class="fas fa-fire text-3xl text-xbox-green mb-6"></i>
            <h3 class="text-2xl font-black text-white italic uppercase mb-2">Populares</h3>
            <p class="text-secondary text-sm font-medium">Os títulos mais quentes do momento.</p>
        </a>
        <a href="top_gratis" class="glass-card p-10 rounded-[2.5rem] bg-gradient-to-br from-blue-500/10 to-transparent border-blue-500/20 group hover:border-blue-500/50 transition-all">
            <i class="fas fa-gift text-3xl text-blue-500 mb-6"></i>
            <h3 class="text-2xl font-black text-white italic uppercase mb-2">Gratuitos</h3>
            <p class="text-secondary text-sm font-medium">Jogue sem gastar nada agora mesmo.</p>
        </a>
        <a href="chegando_em_breve" class="glass-card p-10 rounded-[2.5rem] bg-gradient-to-br from-purple-500/10 to-transparent border-purple-500/20 group hover:border-purple-500/50 transition-all">
            <i class="fas fa-clock text-3xl text-purple-500 mb-6"></i>
            <h3 class="text-2xl font-black text-white italic uppercase mb-2">Em Breve</h3>
            <p class="text-secondary text-sm font-medium">Prepare seu pré-download.</p>
        </a>
    </div>

    <!-- Featured Collections (Simuladas conforme endpoint store-home) -->
    <?php if (!empty($collections)): ?>
        <div class="space-y-20">
            <?php foreach (array_slice($collections, 0, 3) as $collection): ?>
                <section>
                    <div class="flex items-center justify-between mb-8 px-2">
                        <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase"><?= htmlspecialchars($collection['title'] ?? 'Destaques') ?></h3>
                        <button class="text-[10px] font-black text-secondary uppercase tracking-widest hover:text-xbox-green transition-colors">Ver tudo</button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                        <?php foreach (array_slice($collection['Products'] ?? [], 0, 6) as $product): ?>
                            <?php
                                $props = $product['LocalizedProperties'][0] ?? [];
                                $boxArt = 'img/default_game.jpg';
                                foreach ($props['Images'] ?? [] as $img) {
                                    if ($img['ImagePurpose'] === 'BoxArt' || $img['ImagePurpose'] === 'Poster') {
                                        $boxArt = 'https:' . $img['Uri']; break;
                                    }
                                }
                            ?>
                            <a href="jogo?id=<?= $product['ProductId'] ?>" class="group relative flex flex-col gap-3">
                                <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 group-hover:border-xbox-green/50 transition-all duration-500">
                                    <img src="<?= $boxArt ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                </div>
                                <h4 class="text-xs font-bold text-white truncate px-1"><?= htmlspecialchars($props['ProductTitle'] ?? 'Jogo') ?></h4>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
