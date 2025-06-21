<?php
// Inicia a sessão para que possamos salvar os dados do usuário após o login
session_start();

// Informa ao navegador que a resposta será no formato JSON
header('Content-Type: application/json');

require_once("conexao.php"); //

// Começamos com uma resposta padrão de ERRO.

$response = array('status' => 'erro', 'mensagem' => 'email ou senha estão incorretos');

// Verifica se o formulário foi enviado usando o método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    
    // Valida se os campos não estão vazios
    if (!empty($email) && !empty($senha)) {
        
        //  Prepara a consulta ao banco de dados para buscar o usuário pelo e-mail
        // Usar prepare statement previne injeção de SQL
        $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verifica se o usuário foi encontrado (se existe 1 linha com esse e-mail)
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();

                // VERIFICAÇÃO CRÍTICA
                
                if (password_verify($senha, $usuario['senha']) && $usuario['tipo'] === 'paciente') {
                    
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_tipo'] = $usuario['tipo'];
                    
                    $response['status'] = 'ok';
                    $response['mensagem'] = 'Login bem-sucedido!';

                }
            }
            

            $stmt->close();
        }
    }
}

$conn->close();

echo json_encode($response);
?>