<?php
    include('../includes/header.php');
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
?>
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold">Bem-vindo, <?php echo $_SESSION['username']; ?>!</h1>
    <p class="mt-4">Aqui estão algumas informações sobre sua conta Xbox:</p>
    <ul class="mt-4">
        <li>Email: <?php echo $_SESSION['email']; ?></li>
        <li>ID do usuário: <?php echo $_SESSION['user_id']; ?></li>
    </ul>
</div>
<?php include('../includes/footer.php'); ?>