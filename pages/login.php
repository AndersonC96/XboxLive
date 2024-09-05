<?php include('../includes/header.php'); ?>
<div class="flex items-center justify-center min-h-screen bg-gray-900">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex justify-center mb-6">
            <img src="../img/logo.png" alt="Xbox Logo" class="w-24">
        </div>
        <h2 class="text-3xl font-bold text-center text-green-400 mb-6">Entrar na sua Conta</h2>
        <form action="../actions/login_action.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" name="username" id="username" placeholder="Seu username" required class="w-full px-4 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Senha</label>
                <input type="password" name="password" id="password" placeholder="Sua senha" required class="w-full px-4 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-md font-bold focus:outline-none focus:ring-2 focus:ring-green-400 transition duration-200">Entrar</button>
            </div>
            <div class="text-center mt-4">
                <a href="register.php" class="text-sm text-green-400 hover:underline">Ainda não tem uma conta? Cadastre-se</a>
            </div>
        </form>
    </div>
</div>
<?php include('../includes/footer.php'); ?>