<?php
require_once __DIR__ . '/vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();
use Anderson\XboxLive\Core\Database;

$db = Database::getInstance();
$hash = '$2y$10$qpb9A498pAoTTR1kMrha8fYuUu1HJh5Qvo08GOk6gYnJRwefqf';

$stmt = $db->prepare("UPDATE users SET password = :password WHERE username = 'admin'");
if ($stmt->execute(['password' => $hash])) {
    echo "Senha do usuário 'admin' atualizada com sucesso para 'senha123'.";
} else {
    echo "Erro ao atualizar a senha.";
}
