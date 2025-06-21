/* 
----------------------------------------------------------
Função: Exibe mensagem de sucesso e redireciona após login
----------------------------------------------------------
*/
function mostrarMensagemESair(destino) {
    const successBox = document.getElementById('loginSuccess');
    if (successBox) {
        successBox.classList.remove('hidden');
    }
    setTimeout(() => {
        window.location.href = destino;
    }, 2000);
}

/* 
----------------------------------------------------------
Evento: Login Médico
- Envia dados via fetch para loginmedico.php
- Exibe mensagem e redireciona se sucesso
----------------------------------------------------------
*/
const loginMedicoForm = document.querySelector('form.loginmedico');
if (loginMedicoForm) {
    loginMedicoForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const email = this.querySelector('.email').value;
        const senha = this.querySelector('.password').value;

        fetch('php/loginmedico.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                mostrarMensagemESair('telamedico.html');
            } else {
                alert(data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Não foi possível conectar ao servidor.');
        });
    });
}

/* 
----------------------------------------------------------
Evento: Login Paciente
- Envia dados via fetch para loginpaciente.php
- Exibe mensagem e redireciona se sucesso
----------------------------------------------------------
*/
const loginPacienteForm = document.querySelector('form.loginpaciente');
if (loginPacienteForm) {
    loginPacienteForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const email = this.querySelector('.email').value;
        const senha = this.querySelector('.password').value;

        fetch('php/loginpaciente.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                mostrarMensagemESair('telapaciente.html');
            } else {
                alert(data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Não foi possível conectar ao servidor.');
        });
    });
}