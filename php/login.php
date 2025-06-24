<?php
session_start();
require 'conexao.php';

header('Content-Type: application/json');

$email = $_POST['email'];
$senha = $_POST['senha'];

if (empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'E-mail e senha são obrigatórios.']);
    exit();
}

$sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE email = $1 AND tipo = 'paciente'";
$result = pg_query_params($conn, $sql, array($email));

if ($result && pg_num_rows($result) > 0) {
    $usuario = pg_fetch_assoc($result);
    
    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'E-mail ou senha incorretos.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'E-mail ou senha incorretos.']);
}

pg_close($conn);
?>
