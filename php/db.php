<?php
$host = "";
$usuario = "";
$senha = "";
$banco = "control_healthy";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>

