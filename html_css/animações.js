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
// fim animação de sucesso ao fazer login paciente

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
// fim animação de sucesso ao fazer login médico

// Animação de sucesso ao cadastrar usuário
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form.login");
    if (!form) return;

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const nome = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const senha = document.getElementById("password").value;

        if (nome && email && senha) {
            const successBox = document.getElementById("cadastroSuccess");
            if (successBox) {
                successBox.classList.remove("hidden");
                successBox.classList.add("show");
            }

            setTimeout(() => {
                window.location.href = "index.html";
            }, 3000);
        }
    });
});
/*fim animação de sucesso ao cadastrar usuário*/

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
