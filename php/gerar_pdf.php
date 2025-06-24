<?php
session_start();
require 'conexao.php'; // Usa o novo arquivo de conexão para PostgreSQL
require 'fpdf/fpdf.php'; // Inclui a biblioteca para gerar o PDF

// Verifica se o usuário (paciente) está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    die("Acesso não autorizado. Faça login como paciente para gerar seu relatório.");
}

$paciente_id = $_SESSION['usuario_id'];
$paciente_nome = $_SESSION['usuario_nome'];

// 1. Buscar os dados do banco usando as funções do PostgreSQL
$sql = "SELECT TO_CHAR(data_hora, 'DD/MM/YYYY HH24:MI') as data_formatada, sistolica, diastolica 
        FROM registros_pressao 
        WHERE paciente_id = $1 
        ORDER BY data_hora DESC";

$resultado = pg_query_params($conn, $sql, array($paciente_id));

$registros = [];
if ($resultado) {
    // pg_fetch_all pega todos os resultados de uma vez em um array
    $registros = pg_fetch_all($resultado);
}

pg_close($conn); // Já podemos fechar a conexão

// 2. Gerar o PDF com os dados (a lógica do FPDF não muda)
class PDF extends FPDF
{
    // Cabeçalho
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Relatorio de Pressao Arterial - Control Healthy', 0, 1, 'C');
        $this->Ln(10);
    }

    // Rodapé
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    // Tabela de dados
    function BasicTable($header, $data)
    {
        // Cabeçalho da tabela
        $this->SetFont('Arial', 'B', 12);
        foreach ($header as $col) {
            $this->Cell(60, 7, $col, 1);
        }
        $this->Ln();

        // Dados
        $this->SetFont('Arial', '', 12);
        if ($data) {
            foreach ($data as $row) {
                $this->Cell(60, 6, $row['data_formatada'], 1);
                $this->Cell(60, 6, $row['sistolica'], 1);
                $this->Cell(60, 6, $row['diastolica'], 1);
                $this->Ln();
            }
        } else {
            $this->Cell(0, 10, 'Nenhum registro encontrado.', 1, 1, 'C');
        }
    }
}

// Cria uma instância do PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Adiciona o nome do paciente
$pdf->Cell(0, 10, 'Paciente: ' . utf8_decode($paciente_nome), 0, 1);
$pdf->Ln(5);

// Define o cabeçalho da tabela
$header = array('Data e Hora', 'Sistolica (mmHg)', 'Diastolica (mmHg)');

// Adiciona a tabela ao PDF
$pdf->BasicTable($header, $registros);

// Envia o PDF para o navegador
$pdf->Output('D', 'relatorio_pressao.pdf'); // 'D' força o download
?>
