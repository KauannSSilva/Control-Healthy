<?php
require 'conexao.php';

header('Content-Type: application/json');

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$tipo_usuario = $_POST['tipo_usuario'];

if (empty($nome) || empty($email) || empty($senha) || empty($tipo_usuario)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    exit();
}

// Verificar se o e-mail já existe
$sql_check = "SELECT id FROM usuarios WHERE email = $1";
$result_check = pg_query_params($conn, $sql_check, array($email));

if (pg_num_rows($result_check) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'E-mail já cadastrado.']);
    exit();
}

// Criptografar a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir o novo usuário
$sql_insert = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ($1, $2, $3, $4)";
$result_insert = pg_query_params($conn, $sql_insert, array($nome, $email, $senha_hash, $tipo_usuario));

if ($result_insert) {
    echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar usuário.']);
}

pg_close($conn);
?>
