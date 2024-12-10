<?php
session_start();

// Exibir erros para depuração em ambiente de desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email AND status = 'ativo'");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['permissao'] = $user['permissao'];
            $_SESSION['primeiro_acesso'] = $user['primeiro_acesso'];

            header("Location: " . ($user['primeiro_acesso'] ? "redefinir_senha.php" : "index.php"));
            exit;
        } else {
            $erro = "Email ou senha inválidos.";
        }
    } catch (Exception $e) {
        $erro = "Erro ao processar login. Tente novamente mais tarde.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <h1>Login</h1>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
        <?php if (isset($erro)) echo "<p>$erro</p>"; ?>
    </form>
</body>
</html>
