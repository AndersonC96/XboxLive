<main class="auth-bg min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md animate-fade-in">
        <div class="glass-card p-12 rounded-[2.5rem] border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Glow Decor -->
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-xbox-green/20 blur-[80px] rounded-full"></div>

            <div class="text-center mb-12 relative z-10">
                <img src="img/logo2.png" alt="Xbox" class="w-24 h-24 mx-auto mb-8 drop-shadow-[0_0_30px_rgba(16,124,16,0.6)] hover:scale-110 transition-transform duration-500">
                <h1 class="text-4xl font-black tracking-tighter text-white mb-3">Nova Conta</h1>
                <p class="text-secondary font-medium">Junte-se à maior rede de games do mundo.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-5 py-4 rounded-2xl mb-8 text-sm font-bold flex items-center gap-4 animate-shake">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                    <span>
                        <?php 
                            if ($error === 'emptyfields') echo 'Preencha todos os campos.';
                            elseif ($error === 'registrationfailed') echo 'Erro ao registrar. Usuário ou e-mail já existe.';
                            else echo 'Ocorreu um erro. Tente novamente.';
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <form action="register" method="POST" class="space-y-6 relative z-10">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-secondary ml-1">Escolha um Usuário</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="text" name="username" required 
                            class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white outline-none focus:border-xbox-green focus:bg-white/[0.06] transition-all placeholder:text-gray-700 font-medium"
                            placeholder="Ex: MasterChief117">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-secondary ml-1">Seu Melhor E-mail</label>
                    <div class="relative group">
                        <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="email" name="email" required 
                            class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white outline-none focus:border-xbox-green focus:bg-white/[0.06] transition-all placeholder:text-gray-700 font-medium"
                            placeholder="seu@email.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-secondary ml-1">Crie uma Senha Forte</label>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="password" name="password" required 
                            class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white outline-none focus:border-xbox-green focus:bg-white/[0.06] transition-all placeholder:text-gray-700 font-medium"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full btn-xbox py-5 rounded-2xl text-sm tracking-[0.2em] uppercase font-black shadow-[0_10px_30px_rgba(16,124,16,0.3)]">
                        Criar minha conta
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-10 border-t border-white/5 text-center relative z-10">
                <p class="text-secondary font-medium text-sm">
                    Já possui uma conta? 
                    <a href="login" class="text-xbox-green hover:text-xbox-green-light font-black transition-colors ml-1 underline underline-offset-4">Fazer login</a>
                </p>
            </div>
        </div>
    </div>
</main>
