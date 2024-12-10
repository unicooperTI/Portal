<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE usuarios SET senha = :senha, primeiro_acesso = 0 WHERE id = :id");
    $stmt->bindParam(':senha', $nova_senha);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
</head>
<body>
    <form method="POST">
        <h1>Redefinir Senha</h1>
        <input type="password" name="senha" placeholder="Nova senha" required>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>
