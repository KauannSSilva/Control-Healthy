<?php
session_start();
header('Content-Type: application/json');

require_once("conexao.php"); 

$response = array('status' => 'erro', 'mensagem' => 'Credenciais inválidas ou você não possui permissão de médico.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    
    if (empty($email) || empty($senha)) {
        $response['mensagem'] = "E-mail e senha são obrigatórios.";
    } else {
        $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?"); //
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            // VERIFICAÇÃO DUPLA: A senha está correta E o tipo de usuário é 'medico'?
            if (password_verify($senha, $usuario['senha']) && $usuario['tipo'] === 'medico') {
                
                // Se a senha estiver correta e o tipo for 'medico', salva os dados na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                
                $response['status'] = 'ok';
                $response['mensagem'] = 'Login bem-sucedido! Redirecionando...';
            }
        }
        $stmt->close();
    }
}

$conn->close();

echo json_encode($response);
?>