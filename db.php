<?php
$host = 'br976.hostgator.com.br';
$db = 'datsch59_portal';
$user = 'datsch59_sistema'; // Usuário do MySQL
$password = '[Iegn!Ux.#kX'; // Senha do MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>