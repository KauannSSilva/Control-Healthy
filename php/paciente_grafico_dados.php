<?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    echo json_encode(['labels' => [], 'sistolica_data' => [], 'diastolica_data' => []]);
    exit;
}

$paciente_id = $_GET['paciente_id'] ?? 0;
$periodo = $_GET['periodo'] ?? 'daily';
$interval = '1 day';

if ($periodo === 'weekly') {
    $interval = '7 day';
} elseif ($periodo === 'monthly') {
    $interval = '1 month';
}

$sql = "SELECT TO_CHAR(data_hora, 'DD/MM') as dia, AVG(sistolica)::int as media_sistolica, AVG(diastolica)::int as media_diastolica 
        FROM registros_pressao 
        WHERE paciente_id = $1 AND data_hora >= NOW() - INTERVAL '$interval'
        GROUP BY TO_CHAR(data_hora, 'DD/MM'), DATE(data_hora)
        ORDER BY DATE(data_hora)";

$result = pg_query_params($conn, $sql, array($paciente_id));

$labels = [];
$sistolica_data = [];
$diastolica_data = [];

if ($result) {
    $dados = pg_fetch_all($result);
    if ($dados) {
        foreach ($dados as $row) {
            $labels[] = $row['dia'];
            $sistolica_data[] = $row['media_sistolica'];
            $diastolica_data[] = $row['media_diastolica'];
        }
    }
}

echo json_encode([
    'labels' => $labels,
    'sistolica_data' => $sistolica_data,
    'diastolica_data' => $diastolica_data
]);

pg_close($conn);
?>
