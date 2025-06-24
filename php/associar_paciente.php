<?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

$medico_id = $_SESSION['usuario_id'];
$email_paciente = $_POST['email_paciente'] ?? '';

// Encontrar o ID do paciente pelo e-mail
$sql_paciente = "SELECT id FROM usuarios WHERE email = $1 AND tipo = 'paciente'";
$result_paciente = pg_query_params($conn, $sql_paciente, array($email_paciente));

if ($result_paciente && pg_num_rows($result_paciente) > 0) {
    $paciente = pg_fetch_assoc($result_paciente);
    $paciente_id = $paciente['id'];

    // Inserir a associação
    $sql_insert = "INSERT INTO medico_paciente (medico_id, paciente_id) VALUES ($1, $2)";
    $result_insert = pg_query_params($conn, $sql_insert, array($medico_id, $paciente_id));

    if ($result_insert) {
        echo json_encode(['status' => 'success', 'message' => 'Paciente associado com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao associar paciente. Ele já pode estar associado.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Paciente não encontrado com o e-mail fornecido.']);
}

pg_close($conn);
?>
