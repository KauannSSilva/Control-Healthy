<?php
$host = "localhost"; // ou 127.0.0.1
$usuario = "root";   // Usuário padrão do XAMPP
$senha = "";       // Senha padrão do XAMPP é vazia
$banco = "control_healthy"; // <<< VERIFIQUE SE ESTÁ CORRETO ASSIM

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    // Para depuração, é melhor ver o erro exato.
    // Em produção, você pode querer uma mensagem mais genérica.
    die("Erro de conexão com o banco de dados '" . $banco . "': " . $conn->connect_error);
}
?>