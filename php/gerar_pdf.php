<?php
session_start();
require_once('conexao.php');
require('fpdf/fpdf.php');

// VERIFICA SE O PACIENTE ESTÁ LOGADO
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    die("Acesso negado.");
}

$paciente_id = $_SESSION['usuario_id'];

// BUSCA  OS DADOS DO PACIENTE E SEUS REGISTROS
$stmt_paciente = $conn->prepare("SELECT u.nome, u.email, i.endereco, i.telefone FROM usuarios u LEFT JOIN informacoes_paciente i ON u.id = i.usuario_id WHERE u.id = ?");
$stmt_paciente->bind_param("i", $paciente_id);
$stmt_paciente->execute();
$paciente = $stmt_paciente->get_result()->fetch_assoc();

$endereco_formatado = "Nao informado"; // Será corrigido com utf8_decode
if (!empty($paciente['endereco'])) {
    $end_obj = json_decode($paciente['endereco'], true);
    if ($end_obj) {
        $rua = $end_obj['logradouro'] ?? '';
        $num = $end_obj['numero'] ?? '';
        $bairro = $end_obj['bairro'] ?? '';
        $cidade = $end_obj['cidade'] ?? '';
        $uf = $end_obj['uf'] ?? '';
        $endereco_formatado = "$rua, $num - $bairro, $cidade-$uf";
    }
}

$stmt_registros = $conn->prepare("SELECT data_hora, sistolica, diastolica FROM registros_pressao WHERE paciente_id = ? ORDER BY data_hora DESC");
$stmt_registros->bind_param("i", $paciente_id);
$stmt_registros->execute();
$registros = $stmt_registros->get_result();

// 3. CRIA A CLASSE DO PDF COM O CABEÇALHO E RODAPÉ
class PDF extends FPDF {
   function Header() {
        
        $caminho_logo = '../html_css/img/controlhealthy.png';

        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, utf8_decode('Relatório de Saúde'), 0, 1, 'C');
        $this->Ln(5); 

        if (file_exists($caminho_logo)) {
            $largura_logo = 50; 
            $posicao_x = (210 - $largura_logo) / 2;
            
            $this->Image($caminho_logo, $posicao_x, $this->GetY(), $largura_logo);

            $this->SetY($this->GetY() + 35); 
        } else {
            $this->Ln(20);
        }
    }
}

//MONTA O CONTEÚDO DO PDF
$pdf = new PDF();
$pdf->AddPage();

// Seção de Dados do Paciente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Dados do Paciente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, 'Nome: ' . utf8_decode($paciente['nome']), 0, 1);
$pdf->Cell(0, 7, 'Telefone: ' . utf8_decode($paciente['telefone'] ?: 'Não informado'), 0, 1);
$pdf->MultiCell(0, 7, utf8_decode('Endereço: ') . utf8_decode($endereco_formatado), 0, 1);
$pdf->Ln(10);

// Tabela de Histórico de Pressão
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, utf8_decode('Histórico de Pressão Arterial'), 0, 1, 'L');
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(60, 10, 'Data e Hora', 1, 0, 'C', true);
$pdf->Cell(60, 10, utf8_decode('Sistólica (mmHg)'), 1, 0, 'C', true);
$pdf->Cell(60, 10, utf8_decode('Diastólica (mmHg)'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
if ($registros->num_rows > 0) {
    while ($reg = $registros->fetch_assoc()) {
        $pdf->Cell(60, 10, date('d/m/Y H:i', strtotime($reg['data_hora'])), 1, 0, 'C');
        $pdf->Cell(60, 10, $reg['sistolica'], 1, 0, 'C');
        $pdf->Cell(60, 10, $reg['diastolica'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(180, 10, utf8_decode('Nenhum registro de pressão encontrado.'), 1, 1, 'C');
}

// GERA O PDF
$pdf->Output('D', 'Relatorio_ControlHealthy_' . date('Y-m-d') . '.pdf');

$stmt_paciente->close();
$stmt_registros->close();
$conn->close();
?>