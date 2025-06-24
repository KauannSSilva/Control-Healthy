<?php
session_start();
require 'conexao.php'; // Usa o novo arquivo de conexão para PostgreSQL
require 'fpdf/fpdf.php'; // Inclui a biblioteca para gerar o PDF

// Verifica se o usuário (médico) está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    die("Acesso não autorizado. Faça login como médico para gerar o relatório.");
}

// Pega os dados enviados pelo formulário (via POST)
$paciente_id = $_POST['paciente_id'] ?? 0;
$chartImage = $_POST['chartImage'] ?? '';

// Validação básica dos dados recebidos
if (empty($paciente_id) || empty($chartImage)) {
    die("Dados insuficientes para gerar o PDF.");
}

// 1. Buscar o nome do paciente no banco usando as funções do PostgreSQL
$sql_paciente = "SELECT nome FROM usuarios WHERE id = $1";
$result_paciente = pg_query_params($conn, $sql_paciente, array($paciente_id));

$paciente_nome = "Desconhecido";
if ($result_paciente && pg_num_rows($result_paciente) > 0) {
    $paciente = pg_fetch_assoc($result_paciente);
    $paciente_nome = $paciente['nome'];
}

pg_close($conn); // A conexão já pode ser fechada

// 2. Preparar a imagem recebida
// Remove o cabeçalho 'data:image/png;base64,' para obter apenas os dados da imagem
$img_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $chartImage));
// Cria um arquivo temporário em memória para a imagem
$img_file = 'temp_chart_image.png';
file_put_contents($img_file, $img_data);

// 3. Gerar o PDF com os dados (a lógica do FPDF não muda)
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('Gráfico de Evolução da Pressão Arterial'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Cria uma instância do PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Adiciona o nome do paciente
$pdf->Cell(0, 10, 'Paciente: ' . utf8_decode($paciente_nome), 0, 1);
$pdf->Ln(5);

// Adiciona a imagem do gráfico ao PDF
if (file_exists($img_file)) {
    // Adiciona a imagem centralizada na página (largura de 190mm)
    $pdf->Image($img_file, 10, $pdf->GetY(), 190);
    unlink($img_file); // Apaga o arquivo temporário da imagem
} else {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Erro ao carregar a imagem do gráfico.'), 0, 1, 'C');
}

// Envia o PDF para o navegador
$pdf->Output('D', 'relatorio_grafico_pressao.pdf');
?>
