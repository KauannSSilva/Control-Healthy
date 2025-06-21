<?php
session_start();
header('Content-Type: application/json');
require_once("conexao.php");

// Segurança: Garante que um médico esteja logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    echo json_encode([]); // Retorna um array vazio se não houver permissão
    exit;
}

//Validação dos Parâmetros: Pega o ID do paciente e o período da URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([]);
    exit;
}
$paciente_id = $_GET['id'];
$range = $_GET['range'] ?? 'semanal'; // Padrão é 'semanal'

//Monta a condição da query SQL baseada no período
$sql_where_condition = '';
switch ($range) {
    case 'diario':
        $sql_where_condition = "AND DATE(data_hora) = CURDATE()";
        break;
    case 'mensal':
        $sql_where_condition = "AND data_hora >= CURDATE() - INTERVAL 1 MONTH";
        break;
    case 'semanal':
    default:
        $sql_where_condition = "AND data_hora >= CURDATE() - INTERVAL 7 DAY";
        break;
}

//Busca os registros no banco de dados
$stmt = $conn->prepare(
    "SELECT data_hora, sistolica, diastolica 
     FROM registros_pressao 
     WHERE paciente_id = ? $sql_where_condition 
     ORDER BY data_hora ASC"
);
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$result = $stmt->get_result();
$registros = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

// Retorna os dados em formato JSON
echo json_encode($registros);
?>