<?php
session_start();
header('Content-Type: application/json');
require_once("conexao.php");

// Garante que apenas um médico logado possa executar esta ação
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado.']);
    exit;
}

$medico_id = $_SESSION['usuario_id'];
$dados = json_decode(file_get_contents("php://input"), true);
$paciente_email = $dados['email_paciente'] ?? '';

if (empty($paciente_email)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'O e-mail do paciente é obrigatório.']);
    exit;
}

// 1. Encontrar o ID do paciente a partir do e-mail fornecido
$stmt_pac = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND tipo = 'paciente'");
$stmt_pac->bind_param("s", $paciente_email);
$stmt_pac->execute();
$result_pac = $stmt_pac->get_result();

if ($result_pac->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Nenhum paciente encontrado com este e-mail.']);
    exit;
}
$paciente_id = $result_pac->fetch_assoc()['id'];
$stmt_pac->close();

// 2. Verificar se a associação já existe
$stmt_check = $conn->prepare("SELECT medico_id FROM medico_paciente WHERE medico_id = ? AND paciente_id = ?");
$stmt_check->bind_param("ii", $medico_id, $paciente_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Este paciente já está associado a você.']);
    exit;
}
$stmt_check->close();

// 3. Se tudo estiver certo, inserir a nova associação
$stmt_insert = $conn->prepare("INSERT INTO medico_paciente (medico_id, paciente_id) VALUES (?, ?)");
$stmt_insert->bind_param("ii", $medico_id, $paciente_id);

if ($stmt_insert->execute()) {
    echo json_encode(['status' => 'ok', 'mensagem' => 'Paciente associado com sucesso! A lista será atualizada.']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Ocorreu um erro ao associar o paciente.']);
}

$stmt_insert->close();
$conn->close();
?>