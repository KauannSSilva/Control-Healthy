<?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para ATUALIZAR dados
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    // Adicione outros campos como CEP, rua, etc., se necessário

    // Atualiza a tabela de usuários
    $sql_user = "UPDATE usuarios SET nome = $1 WHERE id = $2";
    pg_query_params($conn, $sql_user, array($nome, $usuario_id));

    // Atualiza ou insere na tabela de informações do paciente
    $sql_info_check = "SELECT * FROM informacoes_paciente WHERE usuario_id = $1";
    $res_info_check = pg_query_params($conn, $sql_info_check, array($usuario_id));

    if (pg_num_rows($res_info_check) > 0) {
        $sql_info = "UPDATE informacoes_paciente SET telefone = $1 WHERE usuario_id = $2";
        pg_query_params($conn, $sql_info, array($telefone, $usuario_id));
    } else {
        $sql_info = "INSERT INTO informacoes_paciente (usuario_id, telefone) VALUES ($1, $2)";
        pg_query_params($conn, $sql_info, array($usuario_id, $telefone));
    }

    echo json_encode(['status' => 'success', 'message' => 'Dados atualizados com sucesso!']);

} else {
    // Lógica para BUSCAR dados (GET)
    $sql = "SELECT u.nome, u.email, ip.telefone, ip.endereco FROM usuarios u LEFT JOIN informacoes_paciente ip ON u.id = ip.usuario_id WHERE u.id = $1";
    $result = pg_query_params($conn, $sql, array($usuario_id));
    
    if ($result && pg_num_rows($result) > 0) {
        $dados = pg_fetch_assoc($result);
        echo json_encode($dados);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado.']);
    }
}

pg_close($conn);
?>
