<?php
session_start();
header('Content-Type: application/json');
require_once("conexao.php");

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    echo json_encode([]);
    exit;
}

$medico_id = $_SESSION['usuario_id'];
$pacientes = [];

/*
Esta consulta SQL agora busca os pacientes do médico e, para cada um,
encontra a última medição de pressão registrada.
*/
$sql = "
    SELECT 
        u.id, 
        u.nome, 
        u.email,
        rp.sistolica,
        rp.diastolica,
        rp.data_hora as ultima_afericao
    FROM medico_paciente mp
    JOIN usuarios u ON mp.paciente_id = u.id
    LEFT JOIN (
        SELECT 
            paciente_id, 
            MAX(data_hora) as max_data 
        FROM registros_pressao 
        GROUP BY paciente_id
    ) as max_registros ON max_registros.paciente_id = u.id
    LEFT JOIN registros_pressao rp ON rp.paciente_id = max_registros.paciente_id AND rp.data_hora = max_registros.max_data
    WHERE mp.medico_id = ?
    ORDER BY u.nome ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $medico_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($pacientes);
?>