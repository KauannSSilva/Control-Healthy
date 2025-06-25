<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    header("Location: loginpaciente.html");
    exit();
}
$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="stylesheet" href="style4.css" />
    <link rel="shortcut icon" href="img/favicon2-32x32.png" type="image/x-icon">
    <script src="animações.js" defer></script>
    <title>Painel do Paciente</title>
  </head>
  <body>
    <div class="container">
      <nav class="header">
        <a href="../php/gerar_pdf.php" target="_blank" class="btn orange">Gerar PDF</a>
        <a href="../php/logout.php" class="btn black">Deslogar</a>
      </nav>

      <main class="form-container">
        <h2 id="saudacaoUsuario">Olá, <?php echo $nomeUsuario; ?> (Paciente)</h2>

        <button type="button" id="toggle-info-btn" class="btn btn-toggle">Minhas Informações</button>
        
        <form id="formInformacoes" autocomplete="on" class="hidden">
          <h3>Suas Informações</h3>
          <div class="input-wrapper-paciente">
            <input type="text" class="input nome" id="nome" name="nome" placeholder="Nome Completo" required />
          </div>
          <div class="input-wrapper-paciente">
            <input type="tel" class="input telefone" id="telefone" name="telefone" placeholder="Telefone (com DDD)" />
          </div>
          
          <h4>Endereço</h4>
          <div class="input-wrapper-paciente">
            <input type="tel" class="input endereco" id="cep" name="cep" placeholder="CEP (apenas números)" maxlength="8" />
          </div>
          <div class="input-wrapper-paciente">
            <input type="text" class="input" id="logradouro" name="logradouro" placeholder="Rua / Avenida" readonly />
          </div>
          <div class="input-wrapper-paciente">
            <input type="text" class="input" id="bairro" name="bairro" placeholder="Bairro" readonly />
          </div>
          <div class="input-wrapper-paciente">
            <input type="text" class="input" id="numero" name="numero" placeholder="Número" />
          </div>
          <div class="input-wrapper-paciente">
            <input type="text" class="input" id="complemento" name="complemento" placeholder="Complemento (opcional)" />
          </div>
          <div class="input-wrapper-paciente">
            <input type="text" class="input" id="cidade" name="cidade" placeholder="Cidade" readonly />
          </div>
          <div class="input-wrapper-paciente">
            <input type="text" class="input" id="uf" name="uf" placeholder="Estado" readonly />
          </div>
          <button type="submit" class="btn save">Salvar Informações</button>
        </form>

        <h3>Registrar Pressão</h3>
        <form id="formPressao" autocomplete="off">
          <div class="input-wrapper-paciente">
            <input type="datetime-local" class="input" id="datahora-atual" name="datahora" required />
          </div>
          <div class="input-wrapper-paciente">
            <input type="number" class="input systolic" id="sistolica" name="sistolica" placeholder="Sistólica" required />
          </div>
          <div class="input-wrapper-paciente">
            <input type="number" class="input diastolic" id="diastolica" name="diastolica" placeholder="Diastólica" required />
          </div>
          <button type="submit" class="btn register">Registrar Pressão</button>
        </form>
        
        <div id="feedback-message"></div>
      </main>
    </div>
    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-info-btn');
    const formInformacoes = document.getElementById('formInformacoes');
    const formPressao = document.getElementById('formPressao');
    const feedbackDiv = document.getElementById('feedback-message');
    const cepInput = document.getElementById('cep');

    // LÓGICA DO BOTÃO "MINHAS INFORMAÇÕES" ---
    toggleBtn.addEventListener('click', () => {
        formInformacoes.classList.toggle('hidden');
    });

    // LÓGICA DO CEP (API VIACEP) ---
    const preencherFormularioEndereco = (endereco) => {
        document.getElementById('logradouro').value = endereco.logradouro || '';
        document.getElementById('bairro').value = endereco.bairro || '';
        document.getElementById('cidade').value = endereco.localidade || '';
        document.getElementById('uf').value = endereco.uf || '';
    };

    // Evento que dispara a busca quando o usuário sai do campo CEP
    cepInput.addEventListener('blur', async () => {
        const cep = cepInput.value.replace(/\D/g, ''); 
        if (cep.length === 8) {
            const url = `https://viacep.com.br/ws/${cep}/json/`;
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (!data.erro) {
                    preencherFormularioEndereco(data);
                } else {
                    alert('CEP não encontrado.');
                    preencherFormularioEndereco({}); 
                }
            } catch (error) {
                alert('Não foi possível buscar o CEP. Verifique sua conexão.');
            }
        }
    });

    // CARREGAR DADOS INICIAIS DO PACIENTE ---
    fetch('../php/paciente_dados.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok' && data.dados) {
                document.getElementById('nome').value = data.dados.nome || '';
                document.getElementById('telefone').value = data.dados.telefone || '';
                if (data.dados.endereco) {
                    document.getElementById('cep').value = data.dados.endereco.cep || '';
                    document.getElementById('numero').value = data.dados.endereco.numero || '';
                    document.getElementById('complemento').value = data.dados.endereco.complemento || '';
                    preencherFormularioEndereco(data.dados.endereco);
                }
            }
        });

    // SALVAR INFORMAÇÕES DO PERFIL ---
    formInformacoes.addEventListener('submit', function(e) {
        e.preventDefault();
        const dadosParaSalvar = {
            nome: document.getElementById('nome').value,
            telefone: document.getElementById('telefone').value,
            endereco: {
                cep: document.getElementById('cep').value,
                logradouro: document.getElementById('logradouro').value,
                bairro: document.getElementById('bairro').value,
                cidade: document.getElementById('cidade').value,
                uf: document.getElementById('uf').value,
                numero: document.getElementById('numero').value,
                complemento: document.getElementById('complemento').value
            }
        };
        
        fetch('../php/paciente_dados.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dadosParaSalvar)
        })
        .then(response => response.json())
        .then(data => {
            mostrarFeedback(data.mensagem, data.status);
            if (data.status === 'ok') {
                document.getElementById('saudacaoUsuario').textContent = `Olá, ${data.novo_nome} (Paciente)`;
            }
        });
    });

    // REGISTRAR NOVA MEDIÇÃO DE PRESSÃO ---
    formPressao.addEventListener('submit', function(e) {
        e.preventDefault();
        const dadosPressao = {
            data_hora: document.getElementById('datahora-atual').value,
            sistolica: document.getElementById('sistolica').value,
            diastolica: document.getElementById('diastolica').value
        };

        fetch('../php/registrar_pressao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dadosPressao)
        })
        .then(response => response.json())
        .then(data => {
            mostrarFeedback(data.mensagem, data.status);
            if (data.status === 'ok') {
                // Limpa apenas os campos de medição para um novo registro
                document.getElementById('sistolica').value = '';
                document.getElementById('diastolica').value = '';
            }
        });
    });

    // FUNÇÃO AUXILIAR PARA EXIBIR MENSAGENS ---
    function mostrarFeedback(mensagem, status) {
        feedbackDiv.textContent = mensagem;
        feedbackDiv.style.color = (status === 'ok') ? 'green' : 'red';
        // A mensagem desaparece após 4 segundos
        setTimeout(() => {
            feedbackDiv.textContent = '';
        }, 4000);
    }
});
    </script>
  </body>
</html>