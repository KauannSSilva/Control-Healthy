<?php
session_start();
header('Content-Type: application/json');
require_once('conexao.php');

// 1. Autenticação do usuário
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

$paciente_id = $_SESSION['usuario_id'];

// 2. Verificação do Limite Diário (com lógica corrigida)
// A alteração está aqui: Usamos a função CURDATE() do próprio MySQL,
// que é mais confiável para obter a data atual diretamente no banco.
$stmt_count = $conn->prepare(
    "SELECT COUNT(id) as total_hoje FROM registros_pressao WHERE paciente_id = ? AND DATE(data_hora) = CURDATE()"
);
$stmt_count->bind_param("i", $paciente_id); // Só precisamos do ID do paciente
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row = $result_count->fetch_assoc();
$total_hoje = (int) $row['total_hoje'];
$stmt_count->close();

// Se o total for 2 ou mais, bloqueia o novo registro e avisa o usuário
if ($total_hoje >= 2) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Seu limite diário de 2 registros já foi ultrapassado.']);
    exit;
}

// 3. Processamento do Novo Registro (lógica inalterada)
$dados = json_decode(file_get_contents('php://input'), true);

$data_hora = $dados['data_hora'] ?? '';
$sistolica = $dados['sistolica'] ?? 0;
$diastolica = $dados['diastolica'] ?? 0;

if (empty($data_hora) || empty($sistolica) || empty($diastolica)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Todos os campos de pressão são obrigatórios.']);
    exit;
}

$stmt_insert = $conn->prepare("INSERT INTO registros_pressao (paciente_id, data_hora, sistolica, diastolica) VALUES (?, ?, ?, ?)");
$stmt_insert->bind_param("isii", $paciente_id, $data_hora, $sistolica, $diastolica);

// 4. Envio da Resposta Correta após o INSERT (lógica inalterada)
if ($stmt_insert->execute()) {
    if ($total_hoje == 0) {
        // Este foi o primeiro registro do dia.
        echo json_encode(['status' => 'ok', 'mensagem' => 'Registro salvo! Você ainda pode registrar mais 1 vez hoje.']);
    } else {
        // Este foi o segundo (e último) registro do dia.
        echo json_encode(['status' => 'ok', 'mensagem' => 'Registro salvo! Seu registro diário foi concluído.']);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao salvar o registro no banco de dados.']);
}

$stmt_insert->close();
$conn->close();
?>