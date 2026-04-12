<main class="min-h-[80vh] flex items-center justify-center p-8 animate-fade-in">
    <div class="glass-card p-16 rounded-[3rem] text-center border-white/5 max-w-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-xbox-green/10 blur-[100px] rounded-full"></div>
        
        <div class="w-32 h-32 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-10 border border-white/10 shadow-2xl">
            <i class="fas fa-server text-5xl text-xbox-green"></i>
        </div>
        
        <h1 class="text-6xl font-black text-white italic mb-4 tracking-tighter uppercase">Erro de Sistema</h1>
        <p class="text-secondary text-lg mb-12 font-medium leading-relaxed">
            <?= htmlspecialchars($displayMessage ?? 'Ocorreu um erro interno no servidor.') ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= $baseUrl ?>/dashboard" class="btn-xbox px-10 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl">
                TENTAR NOVAMENTE
            </a>
            <button onclick="location.reload()" class="px-10 py-5 rounded-2xl bg-white/5 text-white font-black uppercase tracking-[0.2em] text-[10px] hover:bg-white/10 transition-all">
                RECARREGAR
            </button>
        </div>
    </div>
</main>
