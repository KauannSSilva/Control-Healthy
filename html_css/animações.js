document.addEventListener("DOMContentLoaded", function () {
  // --- LÓGICA PARA LOGIN DO PACIENTE ---
  const formPaciente = document.querySelector("form.login1");
  if (formPaciente) {
    formPaciente.addEventListener("submit", function (e) {
      e.preventDefault();
      const email = document.getElementById("email").value;
      const senha = document.getElementById("password").value;

      fetch("../php/login_paciente.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(senha)}`,
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === "ok") {
          // Salva o nome do paciente no sessionStorage para usar na próxima página
          sessionStorage.setItem('userName', data.nome);

          const successBox = document.getElementById("loginSuccess");
          if (successBox) {
            successBox.classList.remove("hidden");
            successBox.classList.add("show");
          }
          setTimeout(() => {
            window.location.href = "telapaciente.html";
          }, 3000);
        } else {
          alert("Erro no login: " + data.mensagem);
        }
      })
      .catch(error => {
        console.error("Erro na requisição:", error);
        alert("Não foi possível conectar ao servidor. Verifique o console para detalhes.");
      });
    });
  }

  // --- LÓGICA PARA LOGIN DO MÉDICO ---
  const formMedico = document.querySelector("form.login");
  if (formMedico) {
    formMedico.addEventListener("submit", function (e) {
      e.preventDefault();
      const email = document.getElementById("email").value;
      const senha = document.getElementById("password").value;

      fetch("../php/login_medico.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(senha)}`,
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === "ok") {
           // Salva o nome do médico no sessionStorage
           sessionStorage.setItem('userName', data.nome);

          const successBox = document.getElementById("loginSuccess");
          if (successBox) {
            successBox.classList.remove("hidden");
            successBox.classList.add("show");
          }
          setTimeout(() => {
            window.location.href = "telamedico.html";
          }, 3000);
        } else {
          alert("Erro no login: " + data.mensagem);
        }
      })
      .catch(error => {
        console.error("Erro na requisição:", error);
        alert("Não foi possível conectar ao servidor. Verifique o console para detalhes.");
      });
    });
  }

  // --- SCRIPTS ADICIONAIS ---

  // Validação do campo de telefone para aceitar apenas números
  const telefoneInput = document.getElementById('telefone');
  if (telefoneInput) {
      telefoneInput.addEventListener('input', function (e) {
          this.value = this.value.replace(/\D/g, '').slice(0, 11);
      });
      telefoneInput.addEventListener('keypress', function (e) {
          if (!/[0-9]/.test(e.key)) {
              e.preventDefault();
          }
      });
  }

  // Animação para ocultar/mostrar dados na tela do médico
  const toggleButton = document.querySelector('.toggle-btn');
  if (toggleButton) {
      toggleButton.addEventListener('click', function() {
          const box = document.getElementById('patientsBox');
          box.classList.toggle('hide');
      });
  }

  // Limita o comprimento de inputs numéricos
  document.querySelectorAll('input[type="number"][maxlength]').forEach(function(input) {
      input.addEventListener('input', function() {
        if (this.value.length > 3) {
          this.value = this.value.slice(0, 3);
        }
      });
  });

  // Preenche o input de data/hora com o valor atual
  const dataHoraInput = document.getElementById('datahora-atual');
  if (dataHoraInput) {
      const now = new Date();
      const pad = n => n.toString().padStart(2, '0');
      const local = now.getFullYear() + '-' +
        pad(now.getMonth() + 1) + '-' +
        pad(now.getDate()) + 'T' +
        pad(now.getHours()) + ':' +
        pad(now.getMinutes());
      dataHoraInput.value = local;
  }
});