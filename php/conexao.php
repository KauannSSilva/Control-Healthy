<?php
$host = "localhost"; 
$usuario = "root";   
$senha = "";       
$banco = "control_healthy"; // 

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro de conexão com o banco de dados '" . $banco . "': " . $conn->connect_error);
}
?>