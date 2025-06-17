<?php
session_start();
header('Content-Type: application/json');

// Define uma resposta padrão de erro
$response = ['status' => 'erro', 'mensagem' => 'Ocorreu um erro desconhecido no servidor.'];

try {
    require_once("conexao.php");

    // Verifica se a conexão com o banco de dados falhou
    if ($conn->connect_error) {
        $response['mensagem'] = "Falha na conexão com o banco de dados. Verifique as credenciais em conexao.php.";
        echo json_encode($response);
        exit; // Encerra o script
    }

    // Continua com a lógica de login apenas se a conexão estiver OK
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $senha = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (empty($email) || empty($senha)) {
            $response['mensagem'] = "Email e senha são obrigatórios.";
        } else {
            // Prepara a consulta para buscar o usuário com o email e tipo 'medico'
            $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ? AND tipo = 'medico'");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($senha, $user['senha'])) {
                        $_SESSION['usuario_id'] = $user['id'];
                        $_SESSION['usuario_nome'] = $user['nome'];
                        $_SESSION['usuario_tipo'] = 'medico';
                        
                        $response['status'] = 'ok';
                        $response['mensagem'] = 'Login bem-sucedido!';
                        $response['nome'] = $user['nome'];
                    } else {
                        $response['mensagem'] = 'Email ou senha inválidos.';
                    }
                } else {
                    $response['mensagem'] = 'Email ou senha inválidos ou o usuário não é um médico.';
                }
                $stmt->close();
            } else {
                $response['mensagem'] = "Erro ao preparar a consulta: " . $conn->error;
            }
        }
    } else {
        $response['mensagem'] = "Método de requisição inválido.";
    }

    $conn->close();

} catch (Exception $e) {
    $response['mensagem'] = "Exceção do servidor: " . $e->getMessage();
}

echo json_encode($response);
?>