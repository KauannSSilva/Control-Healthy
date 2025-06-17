<?php
header('Content-Type: application/json');
require_once("conexao.php"); // Inclui sua conexão com o banco de dados

$response = array('status' => 'erro', 'mensagem' => 'Ocorreu um erro desconhecido.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha_formulario = isset($_POST['senha']) ? trim($_POST['senha']) : ''; // Renomeado para evitar confusão com a coluna senha
    // No formulário HTML, o nome do campo para tipo é 'tipoCadastro',
    // mas no banco de dados (tabela usuarios), a coluna é 'tipo'.
    $tipoUsuario = isset($_POST['tipoCadastro']) ? $_POST['tipoCadastro'] : ''; // 'paciente' ou 'medico'

    // Validação básica do lado do servidor
    if (empty($nome) || empty($email) || empty($senha_formulario) || empty($tipoUsuario)) {
        $response['mensagem'] = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['mensagem'] = "Formato de email inválido.";
    } elseif (strlen($senha_formulario) < 6) { // senha com no mínimo 6 caracteres
        $response['mensagem'] = "A senha deve ter pelo menos 6 caracteres.";
    } elseif (!in_array($tipoUsuario, ['paciente', 'medico', 'admin'])) { // Verifica se o tipo é válido conforme o ENUM
        $response['mensagem'] = "Tipo de cadastro inválido.";
    } else {
        // Verifica se o email já existe na tabela 'usuarios'
        $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if ($stmt_check) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $response['mensagem'] = "Este email já está cadastrado.";
            } else {
                // Criptografa a senha para segurança
                $senhaHash = password_hash($senha_formulario, PASSWORD_DEFAULT);

                // Prepara a instrução para inserir os dados na tabela 'usuarios'
                // As colunas são: nome, email, senha, tipo
                $stmt_insert = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
                if ($stmt_insert) {
                    $stmt_insert->bind_param("ssss", $nome, $email, $senhaHash, $tipoUsuario);

                    if ($stmt_insert->execute()) {
                        $response['status'] = "ok";
                        $response['mensagem'] = "Cadastro realizado com sucesso!";
                    } else {
                        $response['mensagem'] = "Erro ao cadastrar no banco de dados: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                } else {
                     $response['mensagem'] = "Erro ao preparar a inserção no banco de dados: " . $conn->error;
                }
            }
            $stmt_check->close();
        } else {
            $response['mensagem'] = "Erro ao preparar a verificação de email: " . $conn->error;
        }
    }
} else {
    $response['mensagem'] = "Método de requisição inválido.";
}

$conn->close();
echo json_encode($response);
?>