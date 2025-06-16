/* Animação de sucesso ao fazer login paciente */
document.addEventListener("DOMContentLoaded", function() {
  const form = document.querySelector("form.login1");
  if (!form) return;

  form.addEventListener("submit", function(e) {
    e.preventDefault();

    // Simula login válido (você pode trocar por validação real)
    const email = document.getElementById("email").value;
    const senha = document.getElementById("password").value;

    if (email && senha) {
      const successBox = document.getElementById("loginSuccess");
      if (successBox) {
        successBox.classList.remove("hidden");
        successBox.classList.add("show");
      }

      setTimeout(() => {
        window.location.href = "telapaciente.html";
      }, 3000); // 3 segundos de animação antes de redirecionar
    }
  });
});
document.getElementById('telefone').addEventListener('input', function (e) {
  // Remove qualquer caractere que não seja número e limita a 11 dígitos
  this.value = this.value.replace(/\D/g, '').slice(0, 11);
});

// Impede a digitação de qualquer caractere que não seja número
document.getElementById('telefone').addEventListener('keypress', function (e) {
  if (!/[0-9]/.test(e.key)) {
    e.preventDefault();
  }
});
// Impede a digitação de qualquer caractere que não seja número

// Animação de sucesso ao fazer login paciente
document.addEventListener("DOMContentLoaded", function() {
  const form = document.querySelector("form.login");
  if (!form) return;

  form.addEventListener("submit", function(e) {
    e.preventDefault();

    // Simula login válido (troque por validação real se necessário)
    const email = document.getElementById("email").value;
    const senha = document.getElementById("password").value;

    if (email && senha) {
      const successBox = document.getElementById("loginSuccess");
      if (successBox) {
        successBox.classList.remove("hidden");
        successBox.classList.add("show");
      }

      setTimeout(() => {
        window.location.href = "telamedico.html"; // redireciona para a tela do médico
      }, 3000); // 3 segundos de animação antes de redirecionar
    }
  });
});
/*
function alertaPressaoAntesDeEnviar() {
  const sistolica = parseInt(document.getElementById('sistolica').value, 10);
  const diastolica = parseInt(document.getElementById('diastolica').value, 10);
  const msgDiv = document.getElementById('mensagem-pressao');

  let mensagem = '';
  let cor = '';

  if (isNaN(sistolica) || isNaN(diastolica)) {
    mensagem = 'Preencha ambos os campos de pressão.';
    cor = 'gray';
  } else if (sistolica < 70 || diastolica < 40) {
    mensagem = 'Pressão muito baixa! Procure um médico imediatamente.';
    cor = 'red';
  } else if (sistolica < 120 && diastolica < 80) {
    mensagem = 'Ótima! Manter hábitos saudáveis.';
    cor = 'green';
  } else if (sistolica <= 120 && diastolica <= 80) {
    mensagem = 'Normal. Manter hábitos saudáveis.';
    cor = 'green';
  } else if ((sistolica >= 121 && sistolica <= 139) || (diastolica >= 81 && diastolica <= 89)) {
    mensagem = 'Pré-Hipertensão. Alerta! Mude o estilo de vida e monitore com mais frequência.';
    cor = 'orange';
  } else if ((sistolica >= 140 && sistolica <= 159) || (diastolica >= 90 && diastolica <= 99)) {
    mensagem = 'Hipertensão Estágio 1. Procure um médico. Mudança de estilo de vida é essencial.';
    cor = 'orange';
  } else if ((sistolica >= 160 && sistolica <= 179) || (diastolica >= 100 && diastolica <= 109)) {
    mensagem = 'Hipertensão Estágio 2. Procure um médico com urgência.';
    cor = 'red';
  } else if (sistolica >= 180 || diastolica >= 110) {
    mensagem = 'Hipertensão Estágio 3. URGÊNCIA/EMERGÊNCIA MÉDICA!';
    cor = 'red';
  } else {
    mensagem = 'Valores fora do padrão. Consulte um médico.';
    cor = 'red';
  }

  msgDiv.innerText = mensagem;
  msgDiv.style.color = cor;

  // Impede o envio do formulário
  return false;
}
//função para exibir o alerta de pressão arterial*/

// animação para ocultar dados da table telamedico.html
document.querySelector('.toggle-btn').addEventListener('click', function() {
  const box = document.getElementById('patientsBox');
  box.classList.toggle('hide');
});
// limita a quantidade de caracteres exibidos na tabela
 document.querySelectorAll('input[type="number"][maxlength]').forEach(function(input) {
        input.addEventListener('input', function() {
          if (this.value.length > 3) {
            this.value = this.value.slice(0, 3);
          }
        });
      });

      // Preenche o input com a data e hora atual do computador no formato correto
  window.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('datahora-atual');
    if (input) {
      const now = new Date();
      const pad = n => n.toString().padStart(2, '0');
      const local = now.getFullYear() + '-' +
        pad(now.getMonth() + 1) + '-' +
        pad(now.getDate()) + 'T' +
        pad(now.getHours()) + ':' +
        pad(now.getMinutes());
      input.value = local;
    }
  });
  // Preenche o input de data/hora com o horário atual do dispositivo e impede edição
  document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('datahora-atual');
    if (input) {
      const now = new Date();
      const pad = n => n.toString().padStart(2, '0');
      const local = now.getFullYear() + '-' +
        pad(now.getMonth() + 1) + '-' +
        pad(now.getDate()) + 'T' +
        pad(now.getHours()) + ':' +
        pad(now.getMinutes());
      input.value = local;
    }
  });
