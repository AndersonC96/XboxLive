<?php
// Pre-processing
if (!$product && $historyTitle) {
    // Fallback Legacy UI
    $name = $historyTitle['name'] ?? 'Título Legado';
    $ach = $historyTitle['achievement'] ?? $historyTitle['achievementInfo'] ?? [];
    $prog = $ach['progressPercentage'] ?? $ach['ProgressPercentage'] ?? 0;
    ?>
    <main class="animate-fade-in pb-20">
        <div class="relative h-[40vh] w-full overflow-hidden bg-xbox-dark">
             <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-transparent to-transparent"></div>
             <div class="absolute inset-0 opacity-10 flex items-center justify-center">
                 <i class="fas fa-compact-disc text-[25rem] animate-spin-slow"></i>
             </div>
             <div class="absolute bottom-0 left-0 w-full p-8 md:p-16 text-center">
                 <span class="text-[10px] font-black uppercase tracking-[0.5em] text-xbox-green mb-4 block">Relíquia Legada (Xbox 360)</span>
                 <h1 class="text-4xl md:text-7xl font-black tracking-tighter text-white italic uppercase"><?= htmlspecialchars($name); ?></h1>
             </div>
        </div>

        <div class="max-w-4xl mx-auto px-8 py-16">
            <div class="glass-card rounded-[3rem] p-12 text-center border-white/5 relative overflow-hidden">
                <h3 class="text-2xl font-black text-white mb-8 italic uppercase tracking-tighter">HISTÓRICO PRESERVADO</h3>
                <p class="text-gray-500 font-medium max-w-lg mx-auto mb-12">
                    Este título não está mais catalogado na loja moderna, mas sua jornada e conquistas continuam salvas na nuvem.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-8 rounded-[2rem] bg-white/5 border border-white/5">
                        <p class="text-[10px] font-black uppercase text-gray-600 mb-2 tracking-widest">Progresso Total</p>
                        <div class="text-4xl font-black text-xbox-green italic"><?= $prog; ?>%</div>
                    </div>
                    <div class="p-8 rounded-[2rem] bg-white/5 border border-white/5">
                        <p class="text-[10px] font-black uppercase text-gray-600 mb-2 tracking-widest">Gamerscore</p>
                        <div class="text-4xl font-black text-white italic"><?= number_format($ach['currentGamerscore'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                </div>
                <div class="mt-16">
                    <a href="dashboard" class="px-12 py-5 rounded-2xl bg-xbox-green text-white font-black uppercase tracking-widest text-[10px] hover:scale-105 transition-all shadow-2xl">
                        VOLTAR À DASHBOARD
                    </a>
                </div>
            </div>
        </div>
    </main>
    <?php return; 
} 

if (!$product) { ?>
    <main class="py-40 px-4 text-center">
        <h3 class="text-3xl font-black text-white mb-4">Título não encontrado</h3>
        <p class="text-secondary mb-12">Este jogo pode ter sido removido ou não está disponível na sua região.</p>
        <a href="todos_os_jogos" class="btn-xbox px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">Explorar Catálogo</a>
    </main>
    <?php return;
}

// Extract Main Info
$props = $product['LocalizedProperties'][0] ?? [];
$title = $props['ProductTitle'] ?? 'Desconhecido';
$description = $props['ProductDescription'] ?? 'Sem descrição disponível.';
$publisher = $props['PublisherName'] ?? 'Xbox';
$developer = $props['DeveloperName'] ?? 'Estúdio';

$marketProps = $product['MarketProperties'][0] ?? [];
$category = $product['Properties']['Category'] ?? 'Digital';

$boxArt = 'img/default_game.jpg';
$heroImage = '';
$screenshots = [];
$images = $props['Images'] ?? [];
foreach ($images as $img) {
    if ($img['ImagePurpose'] === 'BoxArt') $boxArt = 'https:' . $img['Uri'];
    if (in_array($img['ImagePurpose'], ['BrandedKeyArt', 'SuperHeroArt', 'Poster'])) $heroImage = 'https:' . $img['Uri'];
    if ($img['ImagePurpose'] === 'Screenshot') $screenshots[] = 'https:' . $img['Uri'];
}
?>

<main class="animate-fade-in pb-32">
    <!-- Hero Header -->
    <div class="relative h-[70vh] min-h-[600px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 scale-105" 
             style="background-image: url('<?= $heroImage ?: $boxArt; ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark via-xbox-dark/60 to-transparent"></div>
        
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-20">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-end gap-12">
                <div class="w-64 h-64 rounded-[2.5rem] overflow-hidden shadow-[0_40px_80px_rgba(0,0,0,0.9)] border-4 border-white/10 hidden md:block group flex-shrink-0">
                    <img src="<?= $boxArt; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="flex-1 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-4 mb-6">
                        <span class="px-4 py-1.5 rounded-full bg-xbox-green text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-xbox-green/30">Oficial</span>
                        <span class="text-[10px] font-black text-secondary uppercase tracking-[0.3em]"><?= htmlspecialchars($category); ?></span>
                    </div>
                    <h1 class="text-5xl md:text-8xl font-black tracking-tighter text-white mb-8 leading-none italic uppercase"><?= htmlspecialchars($title); ?></h1>
                    
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-10">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-secondary uppercase tracking-widest mb-1">Publicado por</span>
                            <span class="text-sm font-bold text-white"><?= htmlspecialchars($publisher); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-secondary uppercase tracking-widest mb-1">Desenvolvido por</span>
                            <span class="text-sm font-bold text-white"><?= htmlspecialchars($developer); ?></span>
                        </div>
                        <a href="https://www.xbox.com/pt-br/games/store/product/<?= $productId ?>" target="_blank" 
                           class="btn-xbox px-10 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl">
                            VER NA STORE <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-8 md:px-20 py-20 grid grid-cols-1 lg:grid-cols-3 gap-20">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-20">
            
            <!-- Statistics (Optional) -->
            <?php if (!empty($statsData)) : ?>
            <section>
                <h3 class="text-3xl font-black mb-10 flex items-center gap-4 italic tracking-tighter">
                    <span class="w-2 h-8 bg-xbox-green rounded-full"></span>
                    SUA PERFORMANCE
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <?php foreach (array_slice($statsData, 0, 3) as $s) : ?>
                        <div class="glass-card rounded-[2rem] p-8 border-white/5 hover:border-xbox-green/40 transition-all overflow-hidden relative group">
                            <div class="text-[10px] font-black uppercase text-secondary mb-4 tracking-widest"><?= str_replace('_', ' ', $s['name']); ?></div>
                            <div class="text-4xl font-black text-white italic tracking-tighter"><?= $s['value']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Description -->
            <section>
                <h3 class="text-3xl font-black mb-10 flex items-center gap-4 italic tracking-tighter">
                    <span class="w-2 h-8 bg-xbox-green rounded-full"></span>
                    VISÃO GERAL
                </h3>
                <div class="glass-card rounded-[3rem] p-12 border-white/5 relative overflow-hidden">
                    <p class="text-gray-300 leading-relaxed font-medium text-xl opacity-90 whitespace-pre-line">
                        <?= htmlspecialchars($description); ?>
                    </p>
                </div>
            </section>

            <!-- Screenshots -->
            <?php if (!empty($screenshots)) : ?>
            <section>
                <h3 class="text-3xl font-black mb-10 flex items-center gap-4 italic tracking-tighter">
                    <span class="w-2 h-8 bg-xbox-green rounded-full"></span>
                    GALERIA OFICIAL
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <?php foreach ($screenshots as $ss) : ?>
                        <div class="glass-card rounded-[2.5rem] overflow-hidden border-white/5 group relative cursor-pointer">
                            <img src="<?= $ss; ?>" class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-1000">
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-[3rem] p-12 border-white/5 sticky top-12 space-y-12 shadow-2xl">
                <div>
                    <h4 class="text-xs font-black text-secondary uppercase tracking-[0.3em] mb-8 italic">Informações Técnicas</h4>
                    <div class="space-y-8">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-xbox-green border border-white/5 shadow-inner">
                                <i class="fas fa-shield-halved text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-secondary mb-1">Classificação</p>
                                <p class="text-sm font-bold text-white">PEGI / ESRB</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-xbox-green border border-white/5 shadow-inner">
                                <i class="fas fa-cube text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-secondary mb-1">Plataforma</p>
                                <p class="text-sm font-bold text-white">Xbox Series X|S / PC</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-12 border-t border-white/5">
                    <button class="w-full py-5 rounded-2xl bg-white/5 text-white font-black uppercase tracking-[0.2em] text-[10px] hover:bg-xbox-green transition-all shadow-xl">
                        ADICIONAR À LISTA
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
