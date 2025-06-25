<?php
session_start();
require('fpdf/fpdf.php');

// 1. Segurança: Garante que um médico logado possa executar esta ação
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    die("Acesso negado. Por favor, faça o login como médico.");
}

// 2. Validação: Verifica se os dados necessários (imagem e nome) foram enviados
if (empty($_POST['imagem_grafico']) || empty($_POST['paciente_nome'])) {
    die("Erro: Dados insuficientes para gerar o PDF. Tente novamente.");
}

// 3. Recebe e prepara os dados
$paciente_nome = $_POST['paciente_nome'];
$base64_image = $_POST['imagem_grafico'];

// Remove o prefixo "data:image/png;base64," da string da imagem
$base64_image = str_replace('data:image/png;base64,', '', $base64_image);
$base64_image = str_replace(' ', '+', $base64_image);
$image_data = base64_decode($base64_image);

// Cria um arquivo temporário para a imagem, pois o FPDF precisa de um caminho de arquivo
$temp_file = tempnam(sys_get_temp_dir(), 'chart_') . '.png';
file_put_contents($temp_file, $image_data);

// 4. Cria a estrutura do PDF
class PDF extends FPDF {
    // Cabeçalho do PDF
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Relatório de Pressão Arterial'), 0, 1, 'C');
        $this->Ln(5);
    }

    // Rodapé do PDF
    function Footer() {
        $this->SetY(-15); // Posição a 1.5 cm do final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// 5. Gera o PDF
$pdf = new PDF('P', 'mm', 'A4'); // P = Retrato (Portrait), mm = milímetros, A4 = tamanho
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 14);

// Adiciona o nome do paciente
$pdf->Cell(0, 10, utf8_decode('Paciente: ' . $paciente_nome), 0, 1, 'C');
$pdf->Ln(10); // Pula uma linha

// Adiciona a imagem do gráfico
// Largura da imagem de 190mm, centralizada em uma página A4 (210mm de largura)
$pdf->Image($temp_file, 10, 40, 190, 0, 'PNG');

// 6. Limpeza e Saída
unlink($temp_file); // Apaga o arquivo de imagem temporário
$pdf_filename = 'Grafico_Pressao_' . str_replace(' ', '_', $paciente_nome) . '.pdf';
$pdf->Output('I', $pdf_filename); // 'I' envia o PDF para o navegador

?>