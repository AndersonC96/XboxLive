<main class="auth-bg min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md animate-fade-in">
        <div class="glass-card p-12 rounded-[2.5rem] border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Glow Decor -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-xbox-green/20 blur-[80px] rounded-full"></div>
            
            <div class="text-center mb-12 relative z-10">
                <img src="img/logo2.png" alt="Xbox" class="w-24 h-24 mx-auto mb-8 drop-shadow-[0_0_30px_rgba(16,124,16,0.6)] hover:scale-110 transition-transform duration-500">
                <h1 class="text-4xl font-black tracking-tighter text-white mb-3">Bem-vindo</h1>
                <p class="text-secondary font-medium">Sua experiência Xbox Live começa aqui.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-5 py-4 rounded-2xl mb-8 text-sm font-bold flex items-center gap-4 animate-shake">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                    <span>
                        <?php 
                            if ($error === 'emptyfields') echo 'Preencha todos os campos.';
                            elseif ($error === 'wrongcredentials') echo 'Usuário ou senha incorretos.';
                            else echo 'Ocorreu um erro. Tente novamente.';
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
                <div class="bg-xbox-green/10 border border-xbox-green/20 text-xbox-green px-5 py-4 rounded-2xl mb-8 text-sm font-bold flex items-center gap-4 animate-fade-in">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span>Conta criada! Faça seu login abaixo.</span>
                </div>
            <?php endif; ?>

            <form action="login" method="POST" class="space-y-6 relative z-10">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-secondary ml-1">Usuário / Gamertag</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="text" name="username" required 
                            class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white outline-none focus:border-xbox-green focus:bg-white/[0.06] transition-all placeholder:text-gray-700 font-medium"
                            placeholder="Seu nome de usuário">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-secondary ml-1">Senha de Acesso</label>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="password" name="password" required 
                            class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white outline-none focus:border-xbox-green focus:bg-white/[0.06] transition-all placeholder:text-gray-700 font-medium"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full btn-xbox py-5 rounded-2xl text-sm tracking-[0.2em] uppercase font-black shadow-[0_10px_30px_rgba(16,124,16,0.3)]">
                        Entrar na Dashboard
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-10 border-t border-white/5 text-center relative z-10">
                <p class="text-secondary font-medium text-sm">
                    Ainda não tem conta? 
                    <a href="register" class="text-xbox-green hover:text-xbox-green-light font-black transition-colors ml-1 underline underline-offset-4">Crie uma agora</a>
                </p>
            </div>
        </div>
        
        <p class="text-center mt-12 text-gray-700 text-[10px] font-black uppercase tracking-[0.3em] opacity-50">
            &copy; 2026 Xbox Live Project &bull; Design System v2.0
        </p>
    </div>
</main>
