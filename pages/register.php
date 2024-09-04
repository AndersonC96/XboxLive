<?php include('../includes/header.php'); ?>
<div class="flex items-center justify-center h-screen">
    <form action="../actions/register_action.php" method="POST" class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-4">Criar Conta</h2>
        <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
        <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
        <input type="password" name="password" placeholder="Senha" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
        <input type="password" name="confirm_password" placeholder="Confirmar Senha" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
        <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-lg w-full">Registrar</button>
        <p class="mt-4 text-sm text-center">Já tem uma conta? <a href="login.php" class="text-indigo-600">Login</a></p>
    </form>
</div>
<?php include('../includes/footer.php'); ?>
