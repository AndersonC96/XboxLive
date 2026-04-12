<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-fire text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Marketplace Trending</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Mais Jogados</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Os títulos que estão dominando as arenas e corações da comunidade Xbox agora.
            </p>
        </div>
    </header>

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
                ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-xbox-green/50 transition-all duration-500 shadow-2xl">
                        <img src="<?= $boxArt ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <a href="jogo?id=<?= $product['ProductId'] ?>" class="block w-full py-3 bg-xbox-green text-center text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-xbox-green-light transition-colors">
                                Detalhes
                            </a>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-white truncate px-1"><?= htmlspecialchars($title) ?></h3>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <p class="font-black text-gray-600 uppercase tracking-widest text-xs">Nenhum título em destaque no momento</p>
            </div>
        <?php endif; ?>
    </div>
</main>
