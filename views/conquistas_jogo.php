<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto animate-fade-in">
    <header class="mb-16">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-xbox-green/10 border border-xbox-green/20 text-xbox-green">
                <i class="fas fa-medal text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Detalhes do Título</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white">Conquistas</h1>
            <p class="text-secondary text-lg max-w-2xl">Veja seu progresso detalhado e o que ainda falta para o 100%.</p>
        </div>
    </header>

    <div class="space-y-6">
        <?php if (!empty($achievements)): ?>
            <?php 
                $baseUrl = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
                if ($baseUrl === '/') $baseUrl = '';
            ?>
            <?php foreach ($achievements as $ach): ?>
                <?php 
                    // Robust Data Normalization for individual achievements
                    $isLocked = ($ach['progressState'] ?? '') === 'NotStarted' || (!isset($ach['progressState']) && !($ach['unlocked'] ?? false));
                    $isSecret = ($ach['isSecret'] ?? false) && $isLocked;
                    $gsValue = $ach['rewards'][0]['value'] ?? $ach['gamerscore'] ?? 0;
                    $achName = $isSecret ? 'Conquista Secreta' : ($ach['name'] ?? 'Sem Nome');
                    $achDesc = $isSecret ? 'Continue jogando para desbloquear esta conquista.' : ($ach['description'] ?? '');
                    
                    // Icon logic
                    $iconUrl = $ach['mediaAssets'][0]['url'] ?? null;
                    // For 360 games, imageId 52 is very common default, but we don't have the full URL usually.
                    // We'll use a high-quality fallback if no URL is present.
                ?>
                <div class="glass-card group p-8 rounded-[2.5rem] border-white/5 flex flex-col md:flex-row items-center gap-8 <?= $isLocked ? 'opacity-50 grayscale' : 'hover:border-xbox-green/40' ?> transition-all duration-500 relative overflow-hidden">
                    <?php if (!$isLocked): ?>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-xbox-green/5 blur-[50px] -mr-16 -mt-16 rounded-full group-hover:bg-xbox-green/10 transition-all"></div>
                    <?php endif; ?>

                    <!-- Achievement Icon -->
                    <div class="relative flex-shrink-0 z-10">
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 <?= $isLocked ? 'border-gray-800' : 'border-xbox-green shadow-[0_0_30px_rgba(16,124,16,0.3)]' ?> bg-black/40 flex items-center justify-center">
                            <?php if ($isSecret): ?>
                                <i class="fas fa-lock text-3xl text-gray-600"></i>
                            <?php elseif ($iconUrl): ?>
                                <img src="<?= $iconUrl ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-trophy text-3xl <?= $isLocked ? 'text-gray-700' : 'text-xbox-green' ?>"></i>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Achievement Info -->
                    <div class="flex-1 text-center md:text-left min-w-0 z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-3">
                            <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase group-hover:text-xbox-green transition-colors">
                                <?= htmlspecialchars($achName) ?>
                            </h3>
                            <div class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-white/10 shadow-inner group-hover:border-xbox-green/30 transition-all">
                                <img src="<?= $baseUrl ?>/img/gs.png" class="w-4 h-4" alt="GS">
                                <span class="text-lg font-black text-xbox-green"><?= number_format($gsValue, 0, ',', '.') ?></span>
                            </div>
                        </div>
                        <p class="text-gray-400 font-medium text-lg leading-relaxed max-w-2xl">
                            <?= htmlspecialchars($achDesc) ?>
                        </p>
                        
                        <?php 
                            $unlockTime = $ach['progression']['timeUnlocked'] ?? $ach['timeUnlocked'] ?? null;
                            if ($unlockTime && !$isLocked): 
                        ?>
                            <div class="mt-6 flex items-center justify-center md:justify-start gap-3 text-[10px] font-black text-xbox-green uppercase tracking-[0.2em]">
                                <i class="fas fa-check-circle"></i> Desbloqueada em <?= date('d/m/Y', strtotime($unlockTime)) ?>
                            </div>
                        <?php elseif ($isLocked && isset($ach['rarity']['currentPercentage'])): ?>
                            <div class="mt-6 flex items-center justify-center md:justify-start gap-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">
                                <i class="fas fa-users"></i> Rareza: <?= $ach['rarity']['currentPercentage'] ?>% dos jogadores
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="py-40 text-center glass-card rounded-[4rem] border-dashed opacity-50">
                <i class="fas fa-ghost text-6xl mb-8 opacity-10"></i>
                <p class="font-black text-gray-600 uppercase tracking-[0.4em] text-sm">Nenhuma conquista registrada</p>
            </div>
        <?php endif; ?>
    </div>
</main>
