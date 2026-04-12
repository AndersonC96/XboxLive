<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-tags text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Special Offers</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Promoções</h1>
            <p class="text-secondary text-lg max-w-2xl">
                Aproveite os melhores descontos da semana em títulos selecionados da Microsoft Store.
            </p>
        </div>
    </header>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8">
        <?php if (!empty($games)): ?>
            <?php 
                $baseUrl = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
                if ($baseUrl === '/') $baseUrl = '';
            ?>
            <?php foreach ($games as $product): ?>
                <?php
                    $props = $product['LocalizedProperties'][0] ?? [];
                    $images = $props['Images'] ?? [];
                    $boxArt = $baseUrl . '/img/default_game.jpg';
                    
                    $preferredTypes = ['BoxArt', 'Poster', 'TitledHeroArt', 'SuperHeroArt'];
                    $foundImg = null;

                    foreach ($preferredTypes as $type) {
                        foreach ($images as $img) {
                            if ($img['ImagePurpose'] === $type) {
                                $foundImg = $img['Uri'];
                                break 2;
                            }
                        }
                    }

                    if ($foundImg) {
                        $boxArt = str_starts_with($foundImg, '//') ? 'https:' . $foundImg : $foundImg;
                    }
                    
                    $title = $props['ProductTitle'] ?? 'Sem Título';
                ?>
                <div class="group relative flex flex-col gap-3">
                    <div class="glass-card aspect-[2/3] rounded-[1.5rem] overflow-hidden border-white/5 hover:border-xbox-green/50 transition-all duration-500 shadow-2xl relative">
                        <img src="<?= $boxArt ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?= htmlspecialchars($title) ?>">
                        <div class="absolute top-4 right-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg z-10">OFERTA</div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-5">
                            <a href="<?= $baseUrl ?>/jogo?id=<?= $product['ProductId'] ?>" class="block w-full py-3 bg-xbox-green text-center text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-xbox-green-light transition-colors shadow-lg">
                                Detalhes
                            </a>
                        </div>
                    </div>
                    <h3 class="text-[11px] font-black text-white truncate px-1 uppercase tracking-tighter group-hover:text-xbox-green transition-colors"><?= htmlspecialchars($title) ?></h3>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-32 text-center glass-card rounded-[3rem] border-dashed opacity-50">
                <p class="font-black text-gray-600 uppercase tracking-widest text-xs">Novas ofertas em breve</p>
            </div>
        <?php endif; ?>
    </div>
</main>
