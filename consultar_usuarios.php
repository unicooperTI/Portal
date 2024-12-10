<?php
session_start();
require 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['permissao'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Atualizar informações do usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['atualizar'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $permissao = $_POST['permissao'];
        $status = $_POST['status'];

        try {
            $stmt = $conn->prepare("UPDATE usuarios 
                                    SET nome = :nome, sobrenome = :sobrenome, email = :email, permissao = :permissao, status = :status 
                                    WHERE id = :id");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':sobrenome', $sobrenome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':permissao', $permissao);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $mensagem = "Usuário atualizado com sucesso!";
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar usuário: " . $e->getMessage();
        }
    }

    if (isset($_POST['resetar_senha'])) {
        $id = $_POST['id'];
        $nova_senha = password_hash('senha123', PASSWORD_DEFAULT);
        $redefine = '1';  // Mudando para 1 para indicar primeiro acesso

    try {
        $stmt = $conn->prepare("UPDATE usuarios SET senha = :senha, primeiro_acesso = :primeiro_acesso WHERE id = :id");
        $stmt->bindParam(':senha', $nova_senha);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':primeiro_acesso', $redefine);
        $stmt->execute();

            $mensagem = "Senha resetada com sucesso para 'senha123'.";
        } catch (PDOException $e) {
            $erro = "Erro ao resetar senha: " . $e->getMessage();
        }
    }
}

// Obter lista de usuários
try {
    $stmt = $conn->prepare("SELECT * FROM usuarios");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Usuários</title>
</head>
<body>
    <h1>Consultar e Editar Usuários</h1>

    <?php if (isset($mensagem)) echo "<p style='color: green;'>$mensagem</p>"; ?>
    <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Sobrenome</th>
                <th>Email</th>
                <th>Permissão</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <form method="POST">
                        <td><?php echo $usuario['id']; ?></td>
                        <td><input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required></td>
                        <td><input type="text" name="sobrenome" value="<?php echo $usuario['sobrenome']; ?>" required></td>
                        <td><input type="email" name="email" value="<?php echo $usuario['email']; ?>" required></td>
                        <td>
                            <select name="permissao" required>
                                <option value="cooperado" <?php if ($usuario['permissao'] == 'cooperado') echo 'selected'; ?>>Cooperado</option>
                                <option value="usuario" <?php if ($usuario['permissao'] == 'usuario') echo 'selected'; ?>>Usuário</option>
                                <option value="supervisor" <?php if ($usuario['permissao'] == 'supervisor') echo 'selected'; ?>>Supervisor</option>
                                <option value="administrador" <?php if ($usuario['permissao'] == 'administrador') echo 'selected'; ?>>Administrador</option>
                            </select>
                        </td>
                        <td>
                            <select name="status" required>
                                <option value="ativo" <?php if ($usuario['status'] == 'ativo') echo 'selected'; ?>>Ativo</option>
                                <option value="inativo" <?php if ($usuario['status'] == 'inativo') echo 'selected'; ?>>Inativo</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" name="atualizar">Atualizar</button>
                            <button type="submit" name="resetar_senha">Resetar Senha</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php">Voltar para a Página Inicial</a>
</body>
</html>
