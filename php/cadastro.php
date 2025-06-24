<?php
require 'conexao.php'; // Usa o novo arquivo de conexão para PostgreSQL
header('Content-Type: application/json');

// Pega os dados do formulário com segurança, evitando erros se um campo estiver vazio
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$tipo_usuario = $_POST['tipo_usuario'] ?? '';

// Validação simples dos dados recebidos
if (empty($nome) || empty($email) || empty($senha) || empty($tipo_usuario)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    exit();
}

// 1. Verificar se o e-mail já existe no banco
// A query SQL é a mesma, mas a forma de executar é diferente.
// Usamos $1 como placeholder para o primeiro parâmetro, o que previne SQL Injection.
$sql_check = "SELECT id FROM usuarios WHERE email = $1";
$result_check = pg_query_params($conn, $sql_check, array($email));

// Verifica se a query retornou algum resultado
if ($result_check && pg_num_rows($result_check) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'E-mail já cadastrado.']);
    pg_close($conn); // Fecha a conexão
    exit();
}

// 2. Criptografar a senha (isso não muda, é uma função do PHP)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// 3. Inserir o novo usuário no banco
// Novamente, usamos a função pg_query_params para executar a inserção de forma segura.
$sql_insert = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ($1, $2, $3, $4)";
$result_insert = pg_query_params($conn, $sql_insert, array($nome, $email, $senha_hash, $tipo_usuario));

if ($result_insert) {
    echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']);
} else {
    // Se a inserção falhar, retorna um erro genérico
    echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar usuário. Tente novamente.']);
}

// 4. Fecha a conexão com o banco de dados
pg_close($conn);
?>
