<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'medico') {
    header("Location: loginmedico.html");
    exit();
}
$nomeMedico = htmlspecialchars($_SESSION['usuario_nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Médico - Control Healthy</title>
    <link rel="stylesheet" href="style5.css">
    <link rel="shortcut icon" href="img/favicon-32x32.png" type="image/x-icon">
    <script src="animações.js" defer></script>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Control Healthy</h1>
            <div class="medico-info">
                <span>Bem-vindo(a), Dr(a). <?php echo $nomeMedico; ?></span>
                <a href="../php/logout.php" class="btn-logout">Sair</a>
            </div>
        </header>

        <main class="main">
            <section class="associate-patient-section">
                <h3>Associar Novo Paciente</h3>
                <form id="form-associar-paciente">
                    <input type="email" id="email_paciente" placeholder="Digite o e-mail do paciente" required>
                    <button type="submit">Associar</button>
                </form>
                <div id="associate-feedback"></div>
            </section>

            <h2>Seus Pacientes</h2>
            <div class="box">
                <table class="patients-table">
                    <thead>
                        <tr>
                            <th>Nome do Paciente</th>
                            <th>Email</th>
                            <th>Última Aferição</th>
                            <th>Pressão (Sist/Diast)</th>
                        </tr>
                    </thead>
                    <tbody id="patient-list-body">
                        </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('patient-list-body');
        const formAssociar = document.getElementById('form-associar-paciente');
        const associateFeedback = document.getElementById('associate-feedback');

        // Função para carregar a lista de pacientes
        function carregarPacientes() {
            tableBody.innerHTML = '<tr><td colspan="4" class="loading-message">Carregando...</td></tr>';
            fetch('../php/medico_lista_pacientes.php')
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = ''; 
                    if (data.length > 0) {
                        data.forEach(paciente => {
                            const row = document.createElement('tr');
                            row.style.cursor = 'pointer';
                            row.onclick = () => { window.location.href = `detalhes_paciente.php?id=${paciente.id}`; };
                            
                            let ultimaAfericao = 'Nenhuma';
                            let pressao = 'N/A';
                            if (paciente.ultima_afericao) {
                                const dataObj = new Date(paciente.ultima_afericao);
                                ultimaAfericao = dataObj.toLocaleDateString('pt-BR') + ' ' + dataObj.toLocaleTimeString('pt-BR').slice(0,5);
                                pressao = `${paciente.sistolica} / ${paciente.diastolica}`;
                            }
                            
                            row.innerHTML = `
                                <td data-label="Nome">${paciente.nome}</td>
                                <td data-label="Email">${paciente.email}</td>
                                <td data-label="Última Aferição">${ultimaAfericao}</td>
                                <td data-label="Pressão">${pressao}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="4" class="no-patients-message">Você ainda não tem pacientes associados.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar pacientes:', error);
                    tableBody.innerHTML = '<tr><td colspan="4" class="no-patients-message">Não foi possível carregar a lista.</td></tr>';
                });
        }

        // Carrega a lista inicial de pacientes
        carregarPacientes();

        // ADICIONADO DE VOLTA: Lógica para o formulário de associar paciente
        formAssociar.addEventListener('submit', function(e) {
            e.preventDefault();
            const pacienteEmail = document.getElementById('email_paciente').value;
            
            fetch('../php/associar_paciente.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email_paciente: pacienteEmail })
            })
            .then(response => response.json())
            .then(data => {
                associateFeedback.textContent = data.mensagem;
                associateFeedback.style.color = (data.status === 'ok') ? '#27ae60' : '#e74c3c';

                if (data.status === 'ok') {
                    document.getElementById('email_paciente').value = '';
                    setTimeout(() => {
                        associateFeedback.textContent = '';
                        carregarPacientes(); // Atualiza a lista de pacientes dinamicamente
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                associateFeedback.textContent = 'Erro de comunicação com o servidor.';
                associateFeedback.style.color = '#e74c3c';
            });
        });
    });
    </script>
</body>
</html>