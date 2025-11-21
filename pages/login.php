<?php include('../includes/header.php'); ?>
<div class="gamer-background flex items-center justify-center">
    <div class="neon-orb green"></div>
    <div class="neon-orb purple"></div>

    <div class="glass-card-login max-w-lg w-full mx-4">
        <div class="relative z-10">
            <div class="flex justify-between items-center mb-4">
                <div class="badge-soft">
                    <i class="fa-solid fa-circle-nodes"></i>
                    <span>Xbox Live</span>
                </div>
                <div class="text-sm text-gray-300">Acesso gamer seguro</div>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <img src="../img/logo.png" alt="Xbox Logo" class="w-14 drop-shadow-lg">
                <div>
                    <p class="uppercase text-xs tracking-widest text-gray-300">Bem-vindo de volta</p>
                    <h2 class="text-3xl font-extrabold text-white">Entre na sua conta</h2>
                </div>
            </div>

            <form action="../actions/login_action.php" method="POST" class="space-y-5 relative z-10">
                <div class="grid gap-4">
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-200 mb-2">Username</label>
                        <input type="text" name="username" id="username" placeholder="Seu username" required class="w-full px-4 py-3 glass-input" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-200 mb-2">Senha</label>
                        <input type="password" name="password" id="password" placeholder="Sua senha" required class="w-full px-4 py-3 glass-input" />
                    </div>
                </div>

                <div class="divider-line"></div>

                <button type="submit" class="w-full neon-button">
                    Entrar
                </button>

                <div class="text-center mt-5 text-sm text-gray-200">
                    <span class="mr-2">Ainda não tem uma conta?</span>
                    <a href="register.php" class="glass-link">Cadastre-se</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
