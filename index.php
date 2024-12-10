<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nome = $_SESSION['nome'];
$permissao = $_SESSION['permissao'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Inicial</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo $nome; ?>!</h1>

    <?php if ($permissao == 'cooperado'): ?>
        <p>Olá, <?php echo $nome; ?>. Você está logado como cooperado.</p>
    <?php else: ?>
        <nav>
            <ul>
                <li><a href="sistema_mutual.php">Sistema Mutual</a></li>
                <li><a href="marketing.php">Marketing</a></li>
                <li><a href="atendimento.php">Atendimento</a></li>
                <?php if ($permissao == 'supervisor' or $permissao == 'administrador'): ?>
                    <li><a href="unite.php">Unite</a></li>
                    <li><a href="integralização de cotas.php">Integralização de cotas</a></li>
                <?php endif; ?>
                <?php if ($permissao == 'administrador'): ?>
                    <li><a href="registrar_usuario.php">Registrar Novos Usuários</a></li>
                    <li><a href="consultar_usuarios.php">usuarios cadastrados</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <a href="logout.php">Sair</a>
</body>
</html>
