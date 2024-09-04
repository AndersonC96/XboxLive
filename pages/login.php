<?php include('../includes/header.php'); ?>
<div class="flex items-center justify-center h-screen">
    <form action="../actions/login_action.php" method="POST" class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-4">Login</h2>
        <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
        <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
        <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-lg w-full">Login</button>
        <p class="mt-4 text-sm text-center">Não tem uma conta? <a href="register.php" class="text-indigo-600">Registre-se</a></p>
    </form>
</div>
<?php include('../includes/footer.php'); ?>