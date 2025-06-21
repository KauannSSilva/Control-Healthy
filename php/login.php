<?php
// Inicia a sessão para que possamos salvar os dados do usuário após o login
session_start();

// Informa ao navegador que a resposta será no formato JSON
header('Content-Type: application/json');

// Inclui o arquivo de conexão com o banco de dados
require_once("conexao.php"); //

// ETAPA 1: Começamos com uma resposta padrão de ERRO.
// O script só mudará isso para 'ok' se TODAS as verificações passarem.
$response = array('status' => 'erro', 'mensagem' => 'email ou senha estão incorretos');

// Verifica se o formulário foi enviado usando o método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pega os dados do formulário de forma segura
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    
    // Valida se os campos não estão vazios
    if (!empty($email) && !empty($senha)) {
        
        // ETAPA 2: Prepara a consulta ao banco de dados para buscar o usuário pelo e-mail
        // Usar prepare statement previne injeção de SQL
        $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // ETAPA 3: Verifica se o usuário foi encontrado (se existe 1 linha com esse e-mail)
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();

                // ETAPA 4: VERIFICAÇÃO CRÍTICA
                // Compara a senha enviada com a senha criptografada no banco
                // E verifica se o tipo do usuário é 'paciente'
                if (password_verify($senha, $usuario['senha']) && $usuario['tipo'] === 'paciente') {
                    
                    // SUCESSO! Somente neste ponto a resposta é alterada para 'ok'
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_tipo'] = $usuario['tipo'];
                    
                    $response['status'] = 'ok';
                    $response['mensagem'] = 'Login bem-sucedido!';

                }
                // Se a senha ou o tipo estiverem errados, o script não faz nada,
                // mantendo a resposta padrão de 'erro'.
            }
            // Se o e-mail não foi encontrado, o script também não faz nada,
            // mantendo a resposta padrão de 'erro'.

            $stmt->close();
        }
    }
}

$conn->close();

// ETAPA FINAL: Envia a resposta (seja de sucesso ou erro) de volta para o JavaScript
echo json_encode($response);
?>