<main class="auth-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in">
        <div class="glass-card p-10 rounded-2xl border-white/10 shadow-2xl">
            <div class="text-center mb-10">
                <img src="img/logo2.png" alt="Xbox" class="w-20 h-20 mx-auto mb-6 drop-shadow-[0_0_20px_rgba(16,124,16,0.6)]">
                <h1 class="text-3xl font-extrabold tracking-tight text-white mb-2">Criar sua conta</h1>
                <p class="text-gray-400 font-medium">Junte-se à comunidade Xbox Live</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm font-bold flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>
                        <?php 
                            if ($error === 'emptyfields') echo 'Preencha todos os campos.';
                            elseif ($error === 'registrationfailed') echo 'Erro ao registrar. O usuário ou e-mail pode já existir.';
                            else echo 'Ocorreu um erro. Tente novamente.';
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <form action="register" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Usuário</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="text" name="username" required 
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-4 pl-12 pr-4 text-white outline-none focus:border-xbox-green focus:bg-white/[0.08] transition-all placeholder:text-gray-700"
                            placeholder="Seu gamertag ou usuário">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">E-mail</label>
                    <div class="relative group">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="email" name="email" required 
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-4 pl-12 pr-4 text-white outline-none focus:border-xbox-green focus:bg-white/[0.08] transition-all placeholder:text-gray-700"
                            placeholder="seu@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Senha</label>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-xbox-green transition-colors"></i>
                        <input type="password" name="password" required 
                            class="w-full bg-white/5 border border-white/10 rounded-xl py-4 pl-12 pr-4 text-white outline-none focus:border-xbox-green focus:bg-white/[0.08] transition-all placeholder:text-gray-700"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full btn-xbox py-4 rounded-xl text-lg tracking-wide uppercase font-black">
                        Criar Conta
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-white/5 text-center">
                <p class="text-gray-500 font-medium">
                    Já tem uma conta? 
                    <a href="login" class="text-xbox-green hover:text-xbox-green-light font-bold transition-colors">Faça login</a>
                </p>
            </div>
        </div>
    </div>
</main>
