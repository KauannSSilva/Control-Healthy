<?php
session_start();
header('Content-Type: application/json');
require_once('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Se a requisição for GET, busca os dados atuais do paciente
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT u.nome, i.endereco, i.telefone FROM usuarios u LEFT JOIN informacoes_paciente i ON u.id = i.usuario_id WHERE u.id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados_paciente = $result->fetch_assoc();

    if (!empty($dados_paciente['endereco'])) {
        $dados_paciente['endereco'] = json_decode($dados_paciente['endereco'], true);
    }
    
    echo json_encode(['status' => 'ok', 'dados' => $dados_paciente]);
}

// Se a requisição for POST, atualiza os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $novo_nome = trim($dados['nome']);
    $telefone = $dados['telefone'] ?? '';
    $endereco_obj = $dados['endereco'] ?? [];

    if (!empty($novo_nome)) {
        $stmt_nome = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt_nome->bind_param("si", $novo_nome, $usuario_id);
        $stmt_nome->execute();
        $_SESSION['usuario_nome'] = $novo_nome;
    }

    $endereco_json = json_encode($endereco_obj);

    $stmt_check = $conn->prepare("SELECT id FROM informacoes_paciente WHERE usuario_id = ?");
    $stmt_check->bind_param("i", $usuario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $stmt_info = $conn->prepare("UPDATE informacoes_paciente SET endereco = ?, telefone = ? WHERE usuario_id = ?");
        $stmt_info->bind_param("ssi", $endereco_json, $telefone, $usuario_id);
    } else {
        $stmt_info = $conn->prepare("INSERT INTO informacoes_paciente (usuario_id, endereco, telefone) VALUES (?, ?, ?)");
        $stmt_info->bind_param("iss", $usuario_id, $endereco_json, $telefone);
    }

    if ($stmt_info->execute()) {
        echo json_encode(['status' => 'ok', 'mensagem' => 'Dados salvos com sucesso!', 'novo_nome' => $novo_nome]);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao salvar os dados de endereço/telefone.']);
    }
}
?>