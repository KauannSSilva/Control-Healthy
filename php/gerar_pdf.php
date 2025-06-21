<?php
session_start();
require_once('conexao.php');
require('fpdf/fpdf.php');

// 1. VERIFICA SE O PACIENTE ESTÁ LOGADO
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    die("Acesso negado. Por favor, faça o login como paciente.");
}

$paciente_id = $_SESSION['usuario_id'];

// 2. BUSCA TODOS OS DADOS DO PACIENTE E SEUS REGISTROS
$stmt_paciente = $conn->prepare(
    "SELECT u.nome, u.email, i.endereco, i.telefone 
     FROM usuarios u 
     LEFT JOIN informacoes_paciente i ON u.id = i.usuario_id 
     WHERE u.id = ?"
);
$stmt_paciente->bind_param("i", $paciente_id);
$stmt_paciente->execute();
$paciente = $stmt_paciente->get_result()->fetch_assoc();

$endereco_formatado = "Nao informado";
if (!empty($paciente['endereco'])) {
    $end_obj = json_decode($paciente['endereco'], true);
    if ($end_obj) {
        $rua = $end_obj['logradouro'] ?? '';
        $num = $end_obj['numero'] ?? '';
        $bairro = $end_obj['bairro'] ?? '';
        $cidade = $end_obj['cidade'] ?? '';
        $uf = $end_obj['uf'] ?? '';
        $cep = $end_obj['cep'] ?? '';
        $endereco_formatado = "$rua, $num - $bairro, $cidade-$uf, $cep";
    }
}

$stmt_registros = $conn->prepare("SELECT data_hora, sistolica, diastolica FROM registros_pressao WHERE paciente_id = ? ORDER BY data_hora DESC");
$stmt_registros->bind_param("i", $paciente_id);
$stmt_registros->execute();
$registros = $stmt_registros->get_result();

// 3. CRIA A CLASSE DO PDF COM O CABEÇALHO ATUALIZADO
class PDF extends FPDF {
    // Cabeçalho com o logo centralizado abaixo do título
    function Header() {
        // Escreve o título primeiro, centralizado
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'Relatorio de Saude', 0, 1, 'C');
        $this->Ln(5); // Pula um pequeno espaço

        // Define o caminho para a imagem
        $caminho_logo = '../html_css/img/controlhealthy.png';

        if (file_exists($caminho_logo)) {
            // Calcula a posição X para centralizar a imagem
            $largura_logo = 50; // Defina a largura que desejar para o logo
            $posicao_x = (210 - $largura_logo) / 2; // (Largura da página A4 - Largura do logo) / 2
            
            // Insere a imagem
            $this->Image($caminho_logo, $posicao_x, $this->GetY(), $largura_logo);

            // Move o cursor para baixo, para o conteúdo começar após a imagem
            $this->SetY($this->GetY() + 35); 
        } else {
            // Se o logo não for encontrado, apenas pula um espaço maior
            $this->Ln(20);
        }
    }

    // Rodapé
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

// 4. MONTA O RESTANTE DO CONTEÚDO DO PDF
$pdf = new PDF();
$pdf->AddPage();

// Seção de Dados do Paciente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Dados do Paciente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, 'Nome: ' . utf8_decode($paciente['nome']), 0, 1);
$pdf->Cell(0, 7, 'Telefone: ' . ($paciente['telefone'] ?: 'Nao informado'), 0, 1);
$pdf->MultiCell(0, 7, 'Endereco: ' . utf8_decode($endereco_formatado), 0, 1);
$pdf->Ln(10);

// Tabela de Histórico de Pressão
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Historico de Pressao Arterial', 0, 1, 'L');
$pdf->SetFillColor(230, 230, 230); // Fundo cinza claro para o cabeçalho da tabela
$pdf->Cell(60, 10, 'Data e Hora', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Sistolica (mmHg)', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Diastolica (mmHg)', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
if ($registros->num_rows > 0) {
    while ($reg = $registros->fetch_assoc()) {
        $pdf->Cell(60, 10, date('d/m/Y H:i', strtotime($reg['data_hora'])), 1, 0, 'C');
        $pdf->Cell(60, 10, $reg['sistolica'], 1, 0, 'C');
        $pdf->Cell(60, 10, $reg['diastolica'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(180, 10, 'Nenhum registro de pressao encontrado.', 1, 1, 'C');
}

// 5. GERA O PDF
$pdf->Output('D', 'Relatorio_ControlHealthy_' . date('Y-m-d') . '.pdf');

$stmt_paciente->close();
$stmt_registros->close();
$conn->close();
?>