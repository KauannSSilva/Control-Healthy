<?php
session_start();
require 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

$paciente_id = $_SESSION['usuario_id'];
$sistolica = $_POST['sistolica'];
$diastolica = $_POST['diastolica'];

// Aqui você pode adicionar a lógica para limitar 2 registros por dia se desejar
// (Precisaria de uma query SELECT COUNT(*) para o dia atual antes de inserir)

$sql = "INSERT INTO registros_pressao (paciente_id, sistolica, diastolica, data_hora) VALUES ($1, $2, $3, NOW())";
$result = pg_query_params($conn, $sql, array($paciente_id, $sistolica, $diastolica));

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Pressão registrada com sucesso.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar a pressão.']);
}

pg_close($conn);
?>
