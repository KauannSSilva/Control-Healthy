<?php
session_start();

// verificar acesso
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    header("Location: loginmedico.html");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID do paciente inválido ou não fornecido.");
}
$paciente_id = $_GET['id'];
$medico_id = $_SESSION['usuario_id'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/php/conexao.php');

// verificar permissão
$stmt_perm = $conn->prepare("SELECT COUNT(*) FROM medico_paciente WHERE medico_id = ? AND paciente_id = ?");
$stmt_perm->bind_param("ii", $medico_id, $paciente_id);
$stmt_perm->execute();
if ($stmt_perm->get_result()->fetch_row()[0] == 0) {
    die("Você não tem permissão para visualizar este paciente.");
}
$stmt_perm->close();


// buscar dados do paciente
$stmt_paciente = $conn->prepare("SELECT u.nome, u.email, i.endereco, i.telefone FROM usuarios u LEFT JOIN informacoes_paciente i ON u.id = i.usuario_id WHERE u.id = ?");
$stmt_paciente->bind_param("i", $paciente_id);
$stmt_paciente->execute();
$paciente = $stmt_paciente->get_result()->fetch_assoc();
$stmt_paciente->close();

$endereco_formatado = "Não informado";
if (!empty($paciente['endereco'])) {
    $end_obj = json_decode($paciente['endereco'], true);
    if ($end_obj) {
        $endereco_formatado = "{$end_obj['logradouro']}, {$end_obj['numero']} - {$end_obj['bairro']}, {$end_obj['cidade']}-{$end_obj['uf']}";
    }
}

// Busca histórico de pressão em ordem cronológica para o gráfico
$stmt_registros = $conn->prepare("SELECT data_hora, sistolica, diastolica FROM registros_pressao WHERE paciente_id = ? ORDER BY data_hora ASC");
$stmt_registros->bind_param("i", $paciente_id);
$stmt_registros->execute();
$registros = $stmt_registros->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_registros->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes de <?php echo htmlspecialchars($paciente['nome']); ?></title>
    <link rel="stylesheet" href="style6.css">
    <link rel="shortcut icon" href="img/favicon-32x32.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="detail-container">
        <header class="detail-header">
            <div>
                <h1><?php echo htmlspecialchars($paciente['nome']); ?></h1>
                <p>Análise de Dados de Saúde</p>
            </div>
            <a href="telamedico.php" class="btn-back">&larr; Voltar para a lista</a>
        </header>

        <section class="profile-section card">
            <h2>Perfil do Paciente</h2>
            <div class="profile-grid">
                <p><strong>Nome Completo:</strong> <?php echo htmlspecialchars($paciente['nome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($paciente['email']); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($paciente['telefone'] ?: 'Não informado'); ?></p>
                <p><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco_formatado); ?></p>
            </div>
        </section>

        <section class="chart-section card">
            <h2>Evolução da Pressão Arterial</h2>
            <div class="chart-controls">
                <button class="btn-range active" data-range="semanal">Últimos 7 dias</button>
                <button class="btn-range" data-range="mensal">Últimos 30 dias</button>
                <button class="btn-range" data-range="diario">Hoje</button>
                <button id="btn-gerar-pdf" class="btn-range btn-pdf">Gerar PDF</button>
            </div>
            <div class="chart-wrapper">
                <canvas id="pressureChart"></canvas>
            </div>
        </section>

        <section class="history-section card">
            <h2>Histórico de Aferições</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Data e Hora</th>
                            <th>Sistólica (mmHg)</th>
                            <th>Diastólica (mmHg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($registros) > 0): ?>
                            <?php foreach (array_reverse($registros) as $reg): // Inverte para mostrar o mais recente primeiro ?>
                                <tr>
                                    <td data-label="Data e Hora"><?php echo date('d/m/Y H:i', strtotime($reg['data_hora'])); ?></td>
                                    <td data-label="Sistólica (mmHg)"><?php echo $reg['sistolica']; ?></td>
                                    <td data-label="Diastólica (mmHg)"><?php echo $reg['diastolica']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">Nenhum registro de pressão encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <form id="pdf-form" action="../php/gerar_grafico_pdf.php" method="post" target="_blank" style="display: none;">
        <input type="hidden" name="paciente_nome" value="<?php echo htmlspecialchars($paciente['nome']); ?>">
        <input type="hidden" name="imagem_grafico" id="pdf-imagem-grafico">
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const patientId = <?php echo json_encode($paciente_id); ?>;
        const chartCanvas = document.getElementById('pressureChart');
        const rangeButtons = document.querySelectorAll('.btn-range');
        const btnGerarPdf = document.getElementById('btn-gerar-pdf');
        const pdfForm = document.getElementById('pdf-form');
        const pdfImagemGraficoInput = document.getElementById('pdf-imagem-grafico');
        let pressureChartInstance;

        async function fetchAndDrawChart(range) {
            try {
                const response = await fetch(`../php/paciente_grafico_dados.php?id=${patientId}&range=${range}`);
                const registros = await response.json();

                const labels = registros.map(reg => {
                    const data = new Date(reg.data_hora);
                    return range === 'diario' ? 
                        data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }) : 
                        data.toLocaleDateString('pt-BR');
                });
                const sistolicaData = registros.map(reg => reg.sistolica);
                const diastolicaData = registros.map(reg => reg.diastolica);

                const chartData = {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Sistólica',
                            data: sistolicaData,
                            borderColor: '#f3903a',
                            backgroundColor: 'rgba(243, 144, 58, 0.1)',
                            fill: true,
                            tension: 0.1
                        },
                        {
                            label: 'Diastólica',
                            data: diastolicaData,
                            borderColor: '#73b319',
                            backgroundColor: 'rgba(115, 179, 25, 0.1)',
                            fill: true,
                            tension: 0.1
                        }
                    ]
                };

                if (pressureChartInstance) {
                    pressureChartInstance.data = chartData;
                    pressureChartInstance.update();
                } else {
                    pressureChartInstance = new Chart(chartCanvas, {
                        type: 'line',
                        data: chartData,
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                             animation: {
                                onComplete: () => {
                                    chartCanvas.style.backgroundColor = 'white';
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Erro ao buscar dados para o gráfico:', error);
            }
        }

        rangeButtons.forEach(button => {
            button.addEventListener('click', () => {
                if(button.id === 'btn-gerar-pdf') return;
                rangeButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                const selectedRange = button.dataset.range;
                fetchAndDrawChart(selectedRange);
            });
        });

        btnGerarPdf.addEventListener('click', () => {
            if (pressureChartInstance) {
                const imageDataUrl = pressureChartInstance.canvas.toDataURL('image/png');
                pdfImagemGraficoInput.value = imageDataUrl;
                pdfForm.submit();
            } else {
                alert('O gráfico ainda não foi carregado. Por favor, aguarde.');
            }
        });

        fetchAndDrawChart('semanal');
    });
    </script>
</body>
</html>
