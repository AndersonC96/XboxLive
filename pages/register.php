<?php include('../includes/header.php'); ?>
<div class="liquid-background flex items-center justify-center min-h-screen">
    <div class="xbox-orb orb-1"></div>
    <div class="xbox-orb orb-2"></div>
    <div class="xbox-orb orb-3"></div>

    <div class="liquid-card max-w-md w-full mx-4">
        <div class="liquid-glass-effect"></div>
        <div class="xbox-glow"></div>

        <div class="relative z-10 p-8">
            <div class="flex justify-center mb-6">
                <div class="xbox-logo-container">
                    <img src="../img/logo3.png" alt="Xbox Logo" class="w-80 h-24 drop-shadow-2xl">
                </div>
            </div>

            <h2 class="text-4xl font-bold text-white text-center mb-2">Criar Conta</h2>

            <form action="../actions/register_action.php" method="POST" class="space-y-5">
                <div class="input-group">
                    <div class="liquid-input">
                        <i class="fa-solid fa-user input-icon-liquid"></i>
                        <input type="text" name="username" id="username" placeholder="Username" required class="liquid-glass-input" />
                    </div>
                </div>
                <div class="input-group">
                    <div class="liquid-input">
                        <i class="fa-solid fa-envelope input-icon-liquid"></i>
                        <input type="email" name="email" id="email" placeholder="Email" required class="liquid-glass-input" />
                    </div>
                </div>
                <div class="input-group">
                    <div class="liquid-input">
                        <i class="fa-solid fa-lock input-icon-liquid"></i>
                        <input type="password" name="password" id="password" placeholder="Senha" required class="liquid-glass-input" />
                    </div>
                </div>
                <div class="input-group">
                    <div class="liquid-input">
                        <i class="fa-solid fa-lock input-icon-liquid"></i>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar Senha" required class="liquid-glass-input" />
                    </div>
                </div>

                <button type="submit" class="w-full liquid-button xbox-button">
                    <span class="relative z-10">Registrar</span>
                    <div class="xbox-button-glow"></div>
                </button>

                <div class="text-center mt-6 text-sm text-white">
                    <span>Já tem uma conta? </span>
                    <a href="login.php" class="liquid-link xbox-link">Login</a>
                </div>
            </form>
        </div>
    </div>
</div>