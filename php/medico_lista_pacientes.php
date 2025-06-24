<?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    echo json_encode([]);
    exit;
}

$medico_id = $_SESSION['usuario_id'];

$sql = "SELECT u.id, u.nome, u.email, 
               (SELECT rp.sistolica FROM registros_pressao rp WHERE rp.paciente_id = u.id ORDER BY rp.data_hora DESC LIMIT 1) as ultima_sistolica,
               (SELECT rp.diastolica FROM registros_pressao rp WHERE rp.paciente_id = u.id ORDER BY rp.data_hora DESC LIMIT 1) as ultima_diastolica,
               (SELECT rp.data_hora FROM registros_pressao rp WHERE rp.paciente_id = u.id ORDER BY rp.data_hora DESC LIMIT 1) as ultima_data_hora
        FROM usuarios u
        JOIN medico_paciente mp ON u.id = mp.paciente_id
        WHERE mp.medico_id = $1";
        
$result = pg_query_params($conn, $sql, array($medico_id));

if ($result) {
    $pacientes = pg_fetch_all($result);
    echo json_encode($pacientes ?: []);
} else {
    echo json_encode([]);
}

pg_close($conn);
?>
