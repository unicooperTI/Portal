<?php
session_start();
require 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['permissao'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $email = trim($_POST['email']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Remove caracteres não numéricos do CPF
    $senha_inicial = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $permissao = $_POST['permissao'];
    $status = $_POST['status'];

    // Validação simples para CPF
    if (strlen($cpf) != 11 || !is_numeric($cpf)) {
        $erro = "CPF inválido.";
    } else {
        // Inserir o novo usuário no banco de dados
        try {
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, sobrenome, email, cpf, senha, permissao, status) 
                                    VALUES (:nome, :sobrenome, :email, :cpf, :senha, :permissao, :status)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':sobrenome', $sobrenome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':senha', $senha_inicial);
            $stmt->bindParam(':permissao', $permissao);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            $mensagem = "Usuário registrado com sucesso!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código de erro para UNIQUE constraint
                $erro = "Erro: CPF ou Email já cadastrado.";
            } else {
                $erro = "Erro ao registrar usuário: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuário</title>
</head>
<body>
    <h1>Registrar Novo Usuário</h1>

    <?php if (isset($mensagem)) echo "<p style='color: green;'>$mensagem</p>"; ?>
    <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>

    <form method="POST">
        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" id="nome" required><br><br>

        <label for="sobrenome">Sobrenome:</label><br>
        <input type="text" name="sobrenome" id="sobrenome" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="cpf">CPF:</label><br>
        <input type="text" name="cpf" id="cpf" maxlength="14" required><br><br>

        <label for="senha">Senha Inicial:</label><br>
        <input type="password" name="senha" id="senha" required><br><br>

        <label for="permissao">Permissão:</label><br>
        <select name="permissao" id="permissao" required>
            <option value="cooperado">Cooperado</option>
            <option value="usuario">Usuário</option>
            <option value="supervisor">Supervisor</option>
            <option value="administrador">Administrador</option>
        </select><br><br>

        <label for="status">Status:</label><br>
        <select name="status" id="status" required>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
        </select><br><br>

        <button type="submit">Registrar</button>
    </form>

    <br>
    <a href="index.php">Voltar para a Página Inicial</a>
</body>
</html>
